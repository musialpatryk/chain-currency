# Chain currency

This repository contains basic example of blockchain implementation, which could show how blockchain can validate 
if some elements in chain are corrupted. 

## How to start?

```
composer install
composer run client
```

## How to test if chain is being validated?

1. Transfer amount to random id (pass it as sender and receiver).
2. Check ``var/chain`` output.
3. Transfer some amount to different id.
4. Check ``var/chain`` output.
5. Transfer some amount.
6. Change sender, recipient or amount in any line of ``var/chain`` (beside last one).
7. Client should validate chain and prevent from using any actions.