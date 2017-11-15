Mobius DApp Quickstart
========================

A ready-to-run Mobius DApp that can be hooked up to the app store. Just bring
your Mobius developer account!

This example lets users flip a coin. If they win, they keep their MOBI. If
they lose, it goes to the application. This probably won't catch on as a
popular application, so it's up to you to write a better one!

### Requirements

* PHP with SQLite installed
* Mobius Developer account ([sign up here!](https://mobius.network/store/signup))

### Features

* Working DApp right out of the box
* Includes webhook handler that automatically creates users and syncs their balances
* Helpful commands for testing

### Setup

Install with composer:

```text
composer create-project zulucrypto/mobius-dapp-quickstart
```

Enter your API key and App UID when prompted.

Start a web server:

```text
cd mobius-dapp-quickstart
bin/console server:run
```

Go to the URL given in the output from the previous command.

For example: http://localhost:8000/

Then, enter the email address you used when signing up for the DApp store.
After clicking "Simulate Login" you'll see your credit balance.

### Development

**App Store Service**

The DApp store API is available as a service:

```php
// In a controller
$numCredits = $this->getContainer()
    ->get('mobius.app_store')
    ->getBalance('user@example.com');
```

**Helpful Commands**

To check a user's balance:

```bash
$ bin/console mobius:app-store:balance
User   : user@example.com
Balance: 100.0
```

To test the webhook that gets called when a user makes a deposit:

```bash
$ bin/console mobius:simulator:app-store-deposit user@example.com 100
Simulated webhook: user@example.com now has 100 credits.
```

**Example Code**

See `src/AppBundle/Controller/CoinFlipController.php` for the code that implements this DApp