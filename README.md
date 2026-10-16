# SaúdeVue

Sistema de gestão clínica em Laravel 13 + Vue 3. Cada profissional autentica,
cadastra os seus pacientes e mantém o prontuário de cada um — com isolamento
estrito entre contas.

Projeto de estudo: frontend em Vue/Inertia e mensageria com RabbitMQ.

---

## A proposta

O sistema se organiza em três níveis encadeados, e cada nível pertence
exclusivamente ao nível acima dele:

```
Profissional (conta)
 └── Paciente
      └── Prontuário
```

Um profissional nunca vê dados de outro. O prontuário é documento
médico-legal: soft deletes, nunca exclusão física.

---

## Como o sistema foi pensado: multi-tenancy

### O modelo

**Tenant = usuário autenticado.** Cada conta é um silo individual. Não é
multi-empresa com subdomínio — é isolamento por usuário.

Abordagem: **single database, shared schema**, com a coluna `user_id` como
chave de tenant em toda tabela tenant-scoped.

```
users
 └── id, name, email, password

patients
 ├── id
 ├── user_id      ← chave de tenant
 ├── name, cpf, birth_date, phone, email, notes
 └── unique (user_id, cpf)

medical_records
 ├── id
 ├── patient_id   ← vínculo direto
 ├── user_id      ← chave de tenant denormalizada (escopo sem join)
 └── occurred_at, chief_complaint, history, diagnosis, conduct, prescription
```

`user_id` em `medical_records` é propositalmente redundante: permite filtrar
por tenant **sem join**, o que mantém o escopo barato em qualquer query.

O CPF é único **por tenant**, não global — o mesmo CPF pode ser paciente de
dois profissionais diferentes.

### Isolamento em camadas (defesa em profundidade)

| # | Camada | O que faz |
|---|---|---|
| 1 | Banco de dados | FKs obrigatórias, `ON DELETE CASCADE`, índice composto `(user_id, cpf)` |
| 2 | Eloquent | `scopeOwnedBy($user)` aplicado em toda query de domínio |
| 3 | Policies | `PatientPolicy` / `MedicalRecordPolicy` comparam `$model->user_id === $user->id` |
| 4 | Route model binding | binding implícito + policy barram antes do controller agir |
| 5 | Form Requests | validação + `Rule::unique()->where('user_id', ...)` |

Mesmo que alguém esqueça o filtro numa query, a Policy e a FK ainda barram o
vazamento.

> **Nota:** a policy devolve **403**, o que confirma a existência do recurso.
> Em produção multi-tenant costuma-se preferir **404** para não vazar
> existência — troca pequena, feita no binding.

---

## Stack

- **Laravel 13** + PHP 8.3+
- **Breeze 2.4** (sem Fortify — decisão explícita do projeto)
- **Vue 3** + Inertia 2 + Ziggy
- **Tailwind CSS 3** com paleta `brand` (emerald/teal)
- **SQLite** em desenvolvimento, sessão em banco
- **RabbitMQ** para eventos de domínio
- **PHPUnit** (não Pest)

---

## Instalação

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
npm install
npm run build
```

### Modos de execução

| Comando | Sobe | Onde funciona |
|---|---|---|
| `composer run dev` | `serve` + `queue:listen` + `pail` + `vite` | `http://localhost:8000` |
| `composer demo` | build + `serve` + `queue:work` | `localhost` e pela LAN |
| `composer test` | suíte de testes | — |

O `dev` usa `php artisan dev`, que já inclui o worker de fila
(`queue:listen`). O `demo` sobe `serve` e `queue:work` juntos via
`concurrently`, remove o `public/hot` e serve os assets compilados — aí
**apenas a porta 8000** é necessária, sem depender do Vite. Útil para
mostrar a aplicação em outra máquina da rede.

### Testes

```bash
composer test          # ou: php artisan test
```

36 testes, incluindo isolamento entre tenants e resiliência do publisher.

---

## RabbitMQ

O projeto publica **eventos de domínio** no RabbitMQ. Não é a fila de jobs do
Laravel — é a camada de mensageria entre sistemas.

### Instalação

**Ubuntu / WSL:**

```bash
sudo apt update
sudo apt install rabbitmq-server
sudo systemctl enable --now rabbitmq-server
```

**Plugin de gerenciamento** (interface web):

```bash
sudo rabbitmq-plugins enable rabbitmq_management
```

Interface em `http://localhost:15672` — usuário e senha padrão `guest`/`guest`.

> `guest` só autentica em `localhost`. Para acesso remoto é preciso criar um
> usuário dedicado.

**Verificar que está no ar:**

```bash
sudo rabbitmqctl status          # versão e estado
sudo rabbitmqctl list_queues     # filas existentes
```

**Docker (alternativa):**

```bash
docker run -d --name rabbitmq -p 5672:5672 -p 15672:15672 \
  rabbitmq:3-management
```

### Configuração no projeto

Variáveis em `.env` (o `.env.example` traz os mesmos valores):

```dotenv
RABBITMQ_HOST=127.0.0.1
RABBITMQ_PORT=5672
RABBITMQ_USER=guest
RABBITMQ_PASSWORD=guest
RABBITMQ_VHOST=/
RABBITMQ_EXCHANGE=saudevue.eventos
RABBITMQ_ENABLED=true
```

| Variável | Função |
|---|---|
| `RABBITMQ_HOST` / `PORT` | endereço do broker |
| `RABBITMQ_USER` / `PASSWORD` | credenciais. Em produção, um usuário por sistema |
| `RABBITMQ_VHOST` | "sala" lógica dentro do broker. `/` é a padrão |
| `RABBITMQ_EXCHANGE` | exchange direto e durável onde os eventos são publicados |
| `RABBITMQ_ENABLED` | `false` troca o publisher por um em memória (testes/CI) |

### Em produção

O `guest` não serve. Crie um usuário com permissão mínima:

```bash
sudo rabbitmqctl add_user saudevue senha_forte
sudo rabbitmqctl set_permissions -p / saudevue ".*" ".*" ".*"
```

E, se quiser isolar o projeto, um vhost próprio:

```bash
sudo rabbitmqctl add_vhost saudevue
sudo rabbitmqctl set_permissions -p saudevue saudevue ".*" ".*" ".*"
```

---

## Eventos publicados

| Routing key | Quando | Quem dispara |
|---|---|---|
| `patient.registered` | após cadastrar um paciente | `PatientController@store` |
| `patient.updated` | após editar um paciente | `PatientController@update` |

Payload — **somente identificadores**, nada de dado clínico ou pessoal
trafegando pelo broker. Quem consome busca o que precisar no banco dele:

```json
{
  "event_id": "61d1218b-7f4c-493f-a9f4-463fd5f58ad7",
  "event": "patient.registered",
  "occurred_at": "2026-10-16T19:22:58+00:00",
  "data": {
    "patient_id": 1,
    "tenant_id": 3
  }
}
```

### Como foi feito

```
app/
├── Contracts/
│   └── PublishesEvents.php        ← porta de saída (interface)
├── Messaging/
│   ├── AmqpEventPublisher.php     ← publica no RabbitMQ
│   └── NullEventPublisher.php     ← grava em memória (testes/CI)
├── Events/
│   ├── PatientRegistered.php
│   └── PatientUpdated.php
└── Providers/AppServiceProvider.php   ← liga a interface ao publisher
```

O controller depende da **interface**, não do AMQP:

```php
public function store(StorePatientRequest $request, PublishesEvents $publisher): RedirectResponse
{
    $paciente = $request->user()->patients()->create($request->validated());

    $publisher->publish(
        PatientRegistered::ROUTING_KEY,
        PatientRegistered::fromPatient($paciente)->toPayload(),
    );

    return Redirect::route('patients.show', $paciente);
}
```

### Decisões de confiabilidade

O `AmqpEventPublisher` garante:

- **exchange direto e durável** — entrega previsível, sobrevive ao restart
- **delivery_mode persistent** — a mensagem vai para o disco do broker
- **publisher confirms** — só consideramos publicado quando o broker confirma
- **falha nunca propaga** — se o broker cair, o paciente **já está salvo** e a
  requisição continua normalmente. O erro vai para o log.

Existe até um teste cobrindo isso: `test_patient_is_saved_even_when_the_broker_is_down`
aponta para uma porta morta e garante que o cadastro persiste.

### Consumindo os eventos

O produtor não sabe quem escuta. Qualquer sistema que se conecte ao mesmo
broker pode se declarar ouvinte **sem mexer no Laravel**:

```bash
# fila durável do sistema consumidor
rabbitmqadmin declare queue name=auditoria.pacientes durable=true

# declara o que quer escutar (binding key exata)
rabbitmqadmin declare binding source=saudevue.eventos \
    destination=auditoria.pacientes routing_key=patient.registered

# conferir na interface: localhost:15672 -> Queues
```

Como é um **exchange direto**, a mensagem só chega às filas cuja binding key
for **idêntica** à routing key. Publicar `medical_record.added` sem ninguém
ouvindo aquilo resulta em `published but NOT routed` — e é isso que mantém o
desacoplamento.

> O worker do `composer run dev` / `demo` processa **Jobs do Laravel**.
> Consumir os eventos acima exige um consumer próprio, que ainda será
> implementado.

---

## Estrutura do projeto

```
app/
├── Contracts/PublishesEvents.php
├── Events/                    PatientRegistered, PatientUpdated
├── Http/
│   ├── Controllers/           PatientController, MedicalRecordController
│   ├── Middleware/            HandleInertiaRequests
│   └── Requests/              StorePatientRequest, UpdatePatientRequest
├── Messaging/                 AmqpEventPublisher, NullEventPublisher
├── Models/                    User, Patient, MedicalRecord
├── Policies/                  PatientPolicy, MedicalRecordPolicy
└── Rules/Cpf.php              validação mod 11

resources/js/
├── Components/                botões, inputs, modal, logo
├── Layouts/                   AuthenticatedLayout, GuestLayout
└── Pages/
    ├── Auth/                  login, registro, senha
    ├── Patients/              Index, Create, Edit, Show + Partials
    ├── MedicalRecords/        Index
    ├── Dashboard.vue
    └── Welcome.vue

database/
├── migrations/                patients, medical_records
└── factories/                 PatientFactory, MedicalRecordFactory
```

---

## Roadmap

- [x] Autenticação (Breeze + Vue)
- [x] Tema verde
- [x] Cadastro de pacientes com CPF
- [x] Eventos `patient.registered` / `patient.updated` no RabbitMQ
- [ ] Consumers de exemplo (auditoria, notificação)
- [ ] Dead letter queue e retry
- [ ] CRUD de prontuários (aninhado em `patients/{id}/records`)
- [ ] Busca e filtros
- [ ] Trilha de auditoria

---

## Considerações legais

Prontuário é documento médico-legal, com guarda mínima de 20 anos
(Resolução CFM 1.821/2007). Por isso:

- soft deletes em `patients` e `medical_records`
- payload de evento sem dado clínico
- dado de saúde é dado sensível pela LGPD (art. 5º, II)

---

## Licença

MIT
