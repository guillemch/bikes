#Bikes
Fun little projected I created to familiarize myself with the Lumen framework.
It's a simple API that checks the current status of a Valenbisi bike rental station.
It can be combined with IFTT to receive notifications when you enter or exit a location
or at specific times of the day.

You only need to provide the Station ID, which can be found on any Valenbisi network map,
and your intent: whether you'd like to `rent` or `park` a bike at the specified station.
```
/api/station/{station}/{action}
```
It then sends a request to an IFTTT webhook with a notification message, which can
then be passed to the Notifications channel to receive it on your phone,
or any other combination you can think of.
