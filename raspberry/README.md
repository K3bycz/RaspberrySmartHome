# Co minutę
* * * * * /usr/bin/python3 /home/admin/sensors_to_mongodb.py >> /home/admin/sensors_cron.log 2>&1
# Co 10 minut
*/10 * * * * /usr/bin/python3 /home/admin/sensors_to_mongodb.py >> /home/admin/sensors_cron.log 2>&1

# 2>&1 = stdout i stderr (błędy) zapisywane razem
