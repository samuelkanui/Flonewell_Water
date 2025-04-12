# Flonewell Water Management System

A comprehensive water management system that handles customer billing, meter readings, and M-Pesa payments.

## Features

- Customer Management
- Agent Management
- Water Meter Readings
- M-Pesa Payment Integration
- Real-time Usage Tracking
- SMS Notifications
- Role-based Access Control

## Prerequisites

- PHP >= 8.1
- Composer
- Node.js & NPM
- MySQL
- M-Pesa API Credentials
- Twilio Account (for SMS)

## Installation

1. Clone the repository:
```bash
git clone https://github.com/YOUR_USERNAME/Flonewell_Water.git
cd Flonewell_Water
```

2. Install PHP dependencies:
```bash
composer install
```

3. Install JavaScript dependencies:
```bash
npm install
```

4. Create environment file:
```bash
cp .env.example .env
```

5. Generate application key:
```bash
php artisan key:generate
```

6. Configure your database in `.env` file

7. Run migrations:
```bash
php artisan migrate
```

8. Compile assets:
```bash
npm run build
```

## Configuration

1. Update the following in your `.env` file:
   - Database credentials
   - M-Pesa API credentials
   - Twilio credentials
   - Application URL

2. Configure M-Pesa:
   - Set `MPESA_ENV` to either 'sandbox' or 'production'
   - Add your M-Pesa API credentials
   - Set up your callback URL

3. Configure Twilio:
   - Add your Twilio SID and Token
   - Set your Twilio phone number

## Usage

1. Start the development server:
```bash
php artisan serve
```

2. Access the application at `http://localhost:8000`

3. Login with appropriate credentials:
   - Admin
   - Agent
   - Customer

## Roles and Permissions

- **Admin**: Full system access, can manage all users and settings
- **Agent**: Can submit meter readings and manage assigned customers
- **Customer**: Can view usage and make payments

## Payment Integration

The system uses M-Pesa STK Push for payments:
1. Customer initiates payment
2. STK Push is sent to customer's phone
3. Customer completes payment on their phone
4. System automatically updates payment status

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## Security

- All sensitive data is stored securely
- API keys and credentials are never committed to the repository
- Regular security updates are applied

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For support, email support@flonewell.com or create an issue in the repository.
