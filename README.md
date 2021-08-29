## Tool migrate json ghost old version to lastest

## Step 1: Placed old ghost json file 

Placed old ghost json file to `storage/app/public/hapolog.ghost.2021-08-29.json`

## Step 2: Run convert command

```
$ php artisan convert:ghost
```

## Step 3: Re-import new json file to the new ghost app

New file: `storage/app/public/hapolog.ghost.4.json`
