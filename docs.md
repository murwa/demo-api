FORMAT: 1A

# ATM Demo API

# Auth [/auth]
Class AuthController

## Auth [POST /auth]
Auth users by their username and pin. The pin is encrypted before storage.

# User Account [/accounts]
Class AccountController

## Balance [GET /accounts/{account}/balance]
Get the account balance.

## Deposit [POST /accounts/{account}/deposit]
Add money to the account

## Withdrawal [POST /accounts/{account}/withdraw]
Withdraw from the Account. The amount to withdraw must be a positive integer, and not more than the available
balance

## Transfer [POST /accounts/{account}/transfer]
Move money from Account A to another account B. The amount to be transfered from Account A must be lower than
the balance in A, and be positive amount

## User Accounts [GET /accounts]
List user's accounts

## Account [GET /accounts/{account}]
Get a single account by account number