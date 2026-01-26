<x-mail::message>
# Google Ads Havi Riport

Tisztelt Partnerünk!

Csatolmányként megküldtük a **{{ $team->name }}** Google Ads fiókjának havi riportját az alábbi időszakra:

**{{ $reportMonth->translatedFormat('Y. F') }}**

A riportban megtalálhatja a kampányok teljesítményét, a főbb mutatókat, valamint a demográfiai és földrajzi bontásokat.

Ha kérdése van a riporttal kapcsolatban, kérjük vegye fel velünk a kapcsolatot.

Üdvözlettel,<br>
{{ config('app.name') }}

<x-mail::subcopy>
Ez az email automatikusan lett generálva. Kérjük, ne válaszoljon rá közvetlenül.
</x-mail::subcopy>
</x-mail::message>
