# Ecoflow\Core
The basic core of ecoflow packages.
Core features.

## Commands
`php artisan ecoflow:checkdb`
Check if all tables of ecoflow installed packages exists.
Will automatically know what tables we have in our migration files.

## BaseRepository
All Repositories need to extends from this BaseRepository.
It contains all primary opearation on models.


### **--Changelog--**
### **v0.0.3** 

`ecoflow:checkdb` Auto discover tables in migrations and check if **exists**
