# Foodly

**Foodly** is a full multi-role food-ordering platform built with **PHP, MySQL/MariaDB, and vanilla JS/AJAX**. It connects four kinds of users, each with a purpose-built interface:

- 🍽️ **Customers** browse restaurants, order food, track deliveries live, and chat with support.
- 👨‍🍳 **Restaurants** manage their menu, accept or decline orders, and see demand predictions.
- 🛵 **Riders** pick up jobs, go on the way, and confirm delivery with a one-time code.
- 💬 **Support agents** work a ticket inbox, assign chats to themselves, and reply to customers.

> The frontend was fully redesigned around a single editorial design system (warm cream + terracotta, Young Serif + Hanken Grotesk), and the auth layer hardened with prepared statements and hashed passwords.

## Features

- **Role-based dashboards** for customers, restaurants, riders, and support.
- **Live order tracking** with a Placed → Accepted → On the way → Delivered stepper and delivery OTP.
- **Support ticketing** — one ticket per customer, assignable to an agent, with live two-way chat.
- **Cart & checkout** with quantity steppers, GST, savings, and working coupon codes (`FOODLY10`, `FOODLY20`, `WELCOME`).
- **Restaurant tools** — menu CRUD, online/offline toggle, order management, demand recommendations.
- **Food donation** board for restaurants to list surplus food.
- **Hardened auth** — prepared statements (no SQL injection), `password_hash()` / `password_verify()`, session regeneration.
- Responsive, mobile-friendly, with a shared design-token system.

## Tech stack

| Layer | Tech |
|-------|------|
| Backend | PHP 8.2 (Apache) |
| Database | MariaDB 10.11 |
| Frontend | Vanilla JS, jQuery (AJAX), CSS custom properties |
| Infra | Docker + Docker Compose |

## Quick start (Docker)

**Prerequisites:** [Docker](https://docs.docker.com/get-docker/) and Docker Compose.

```bash
git clone https://github.com/namanvashistha/foodly.git
cd foodly
cp .env.example .env
docker network create caddy        # one-time (the base compose attaches to an external 'caddy' network)
docker compose up -d --build
```

This is the **production** setup: the app is served via Caddy on the external `caddy` network (no host port published).

The database is created and seeded automatically from [`setup.sql`](setup.sql) on first run.

To stop:

```bash
docker compose down
```

### Local development

For local dev, layer in [`docker-compose.dev.yml`](docker-compose.dev.yml), which publishes a host port and bind-mounts the source for live editing:

```bash
docker compose -f docker-compose.yml -f docker-compose.dev.yml up -d --build
```

Then open **http://localhost:8000**.

### Demo accounts

| Role | Email | Password |
|------|-------|----------|
| Customer | `admin` | `admin` |
| Restaurant | `contact@pizzapalace.test` | `pizza123` |
| Rider | `speedy@rider.test` | `rider123` |
| Support | `henry@support.test` | `support123` |

## Alternative: XAMPP

1. Install [XAMPP](https://www.apachefriends.org/download.html) and clone this repo into `xampp/htdocs`.
2. Start Apache and MySQL, then import [`setup.sql`](setup.sql) via phpMyAdmin.
3. Update [`connection.php`](connection.php) credentials if they differ from the defaults.
4. Open `http://localhost/foodly`.

## Documentation

1. [Role-Based Interfaces](docs/01_role_based_interfaces.md)
2. [User Authentication Feedback](docs/02_user_authentication_feedback.md)
3. [Dynamic Item Entry](docs/03_dynamic_item_entry.md)
4. [UI Dropdown Component](docs/04_ui_dropdown_component.md)
5. [Project Setup & Contribution Guidelines](docs/05_project_setup___contribution_guidelines.md)

## Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for our code of conduct and the pull-request process.

## License

MIT — see [LICENSE](LICENSE).
