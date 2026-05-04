# Cron – harmonogram uruchamiania skryptu

## Co minutę
```bash
* * * * * /usr/bin/python3 /home/admin/sensors_to_mongodb.py >> /home/admin/sensors_cron.log 2>&1
```

## Co 10 minut
```bash
*/10 * * * * /usr/bin/python3 /home/admin/sensors_to_mongodb.py >> /home/admin/sensors_cron.log 2>&1
```

## Wyjaśnienie

- `2>&1` – przekierowanie **stderr (błędów)** do **stdout**, dzięki czemu wszystko trafia do jednego pliku logów
