# Bikes

Fun little project I created to familiarize myself with the __Lumen framework__.
It's a simple API that checks the current status of a __Valenbisi__ bike rental station,
although other services could be added. It can be combined with __IFTT__ to receive 
notifications when you enter or exit a location or at specific times of the day.

You only need to provide the _Station ID_, which can be found on any Valenbisi network map,
and your intent: whether you'd like to `rent` or `park` a bike at the specified station.
```
/api/station/{station}/{action}
```
It then sends a request to an IFTTT webhook with a notification message, which can
then be passed to the Notifications channel to receive it on your phone,
or any other combination you can think of.
