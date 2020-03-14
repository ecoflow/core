# Ecoflow\Core
The basic core of ecoflow packages.
Core features.

## Commands
`php artisan ecoflow:checkdb`
Check if all tables of ecoflow installed packages exists.

## BaseRepository
All Repositories need to extends from this BaseRepository
It contains all primary opearation on models.

## db.json
This file need to be always updated when we add new tables in any ecoflow pacakge.
This part need to be more automatic. 
We need to parse (*Database/migrations* folder) for each ecoflow package and determine tables.
