# Invox: Invoicing Made Easy

## Overview

Invox is a comprehensive invoicing application designed to simplify and streamline the process of creating and managing
invoices. Tailored for freelancers, small businesses, and anyone in need of an efficient invoicing solution, Invox
offers a user-friendly interface and a suite of powerful features to enhance your invoicing workflow.

## Features

- **Invoice & Quote Management**: Easily create, edit, and remove quotes and invoices with key details like client info,
  products, and taxes.
- **Client Management**: Full client data management with a neat record of all interactions.
- **Product & Category Organization**: Add and organize products in categories for easy quoting and billing.
- **Automated Email Dispatch**: Send quotes and invoices via automated emails, complete with customization and document
  links.
- **Payment Tracking**: Monitor invoice payments and update payment statuses for a clear view of finances.
- **User & Access Control**: Manage user roles and permissions to secure data and ensure privacy.

## Installation

### Using Docker

To simplify the setup process and ensure a consistent environment across all devices, Invox can be run using Docker.
Follow these steps to get started:

1. **Clone the repository:**
    ```bash
    git clone https://github.com/MrIsmail1/Invox

2. **Navigate to the project directory:**
    ```bash
    cd invox

3. **Build the Docker containers:**
    ```bash
    docker compose build php --no-cache --pull

4. **Start the Docker containers:**
    ```bash
    docker-compose up -d

5. **Access the application:** Open your web browser and navigate to `http://localhost` (or the port you configured in
   your `docker-compose.yml`).

Note: Make sure Docker and Docker Compose are installed on your machine. For detailed Docker setup and configuration
instructions, please refer to the [Docker Installation Guide](https://docs.docker.com/get-docker/).

## Usage

After installation, you can start using Invox to create and manage your invoices. Here's how to get started:

1. **Create a New Invoice**: Navigate to the 'Create Invoice' section and fill out the necessary details.
2. **Manage Clients**: Add and manage your clients through the 'Clients' section.
3. **View Reports**: Access the 'Reports' section for analytics and insights into your invoicing activity.

For more detailed instructions, check out the [User Guide](#).

## Contributing

We welcome contributions to Invox! If you have suggestions for improvements or bug fixes, please feel free to
contribute. Check out our [Contribution Guidelines](#) for more information.

## Collaborators

- **Ismail MRABET**
- **Hamza MAHMOOD**

Special thanks to Ismail MRABET and Hamza MAHMOOD for their invaluable contributions to the development and success of
Invox.

## License

Invox is released under the [MIT License](LICENSE). See the LICENSE file for more details.