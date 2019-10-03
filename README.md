### Установка приложения: 
1. `docker-compose up -d`
2. `php bin/console doctrine:migrations:migrate`

### Примеры запросов

- Добавление
 
```curl -X POST \
     http://sport.local:23180/api/game \
     -H 'Content-Type: application/json' \
     -H 'Postman-Token: 64c9bf8d-cfd6-4228-8d59-70739b291b44' \
     -H 'cache-control: no-cache' \
     -d '{
   	"lang": "en",
   	"type": "footbal",
   	"league": "UEFA",
   	"team1_name": "Loko",
   	"team2_name": "Sheriff",
   	"started_at": "2019-10-02 23:45:00",
   	"source": "sportdata4.com"
   }'
```

- Случайный матч

```curl -X GET \
     'http://sport.local:23180/api/game/random?source=sportdata2.com&startedFrom=2019-10-02%2023:00:00&startedTo=' \
     -H 'Content-Type: application/json' \
     -H 'Postman-Token: 0251d685-2fe6-44f6-9d6f-b79aee41faa6' \
     -H 'cache-control: no-cache'
```
