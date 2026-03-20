<?php 

namespace App\Service;

use App\Entity\Event;

class EventCapacityManager
{
    public function isEventFull(Event $event): bool
    {   
        $bookedPlaces = $event->getParticipants()->count();
        $maxCapacity = $event->getCapaciteMax(); 
        // Logique pour vérifier si l'événement est complet
        // Par exemple, comparer le nombre de participants avec la capacité maximale
        return $bookedPlaces >= $maxCapacity; // Placeholder, à implémenter selon votre logique métier
    }

    public function getAvailablePlaces(Event $event): int
    {
        $bookedPlaces = $event->getParticipants()->count();
        $maxCapacity = $event->getCapaciteMax(); 
        // Logique pour calculer les places disponibles
        return $maxCapacity - $bookedPlaces; // Placeholder, à implémenter selon votre logique métier
    }
}