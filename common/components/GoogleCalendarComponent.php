<?php

namespace common\components;

use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventDateTime;

date_default_timezone_set('Asia/Baku');

const CALENDAR_ID = 'sanan@dillbill.com';
const CREDENTIALS_PATH = __DIR__ . '/service_account.json';
const SCOPES = Google_Service_Calendar::CALENDAR;

class GoogleCalendarComponent
{
    public function createEvent($summary, $description, $timeZone, $startsAt, $endsAt, $minutesBefore = 30)
    {
        $googleClient = new Google_Client();
        $googleClient->setApplicationName("Google Calendar");
        $googleClient->setAuthConfig(CREDENTIALS_PATH);
        $googleClient->setScopes([SCOPES]);
        $googleClient->setSubject(CALENDAR_ID);
        $calendarService = new Google_Service_Calendar($googleClient);

        $event = new Google_Service_Calendar_Event(array(
            'summary' => $summary,
            'description' => $description,

            'start' => array(
                'dateTime' => $startsAt,
                'timeZone' => $timeZone,
            ),
            'end' => array(
                'dateTime' => $endsAt,
                'timeZone' => $timeZone,
            ),

            'reminders' => array(
                'useDefault' => FALSE,
                'overrides' => array (
                    array('method' => 'email', 'minutes' => $minutesBefore),
                    array('method' => 'popup', 'minutes' => $minutesBefore),
                ),
            ),
        ));

        $events = $calendarService->events->insert(CALENDAR_ID, $event);
        //echo '<pre>'; print_r($events->id); echo '</pre>';

        return $events->id;
    }


    public function addAttendee($eventId, $email)
    {
        $googleClient = new Google_Client();
        $googleClient->setApplicationName("Google Calendar");
        $googleClient->setAuthConfig(CREDENTIALS_PATH);
        $googleClient->setScopes([SCOPES]);
        $googleClient->setSubject(CALENDAR_ID);
        $calendarService = new Google_Service_Calendar($googleClient);

        $createdEvent = $calendarService->events->get(CALENDAR_ID, $eventId);
        $attendees = $createdEvent->getAttendees();
        $users = array();

        foreach ($attendees as $key => $value) {
            array_push($users, array('email' => $value['email']));
        }

        array_push($users, array('email' => $email));

        $createdEvent->setAttendees(
            $users
        );

        $updatedEvent = $calendarService->events->update(CALENDAR_ID, $createdEvent->getId(), $createdEvent);
        //echo '<pre>'; print_r($updatedEvent); echo '</pre>';
    }


    public function updateDescription($eventId, $description)
    {
        $googleClient = new Google_Client();
        $googleClient->setApplicationName("Google Calendar");
        $googleClient->setAuthConfig(CREDENTIALS_PATH);
        $googleClient->setScopes([SCOPES]);
        $googleClient->setSubject(CALENDAR_ID);
        $calendarService = new Google_Service_Calendar($googleClient);

        $createdEvent = $calendarService->events->get(CALENDAR_ID, $eventId);

        $createdEvent->setDescription(
            $description
        );

        $updatedEvent = $calendarService->events->update(CALENDAR_ID, $createdEvent->getId(), $createdEvent);
    }


    public function updateClassTime($eventId, Google_Service_Calendar_EventDateTime $startsAt, Google_Service_Calendar_EventDateTime $endsAt)
    {
        $googleClient = new Google_Client();
        $googleClient->setApplicationName("Google Calendar");
        $googleClient->setAuthConfig(CREDENTIALS_PATH);
        $googleClient->setScopes([SCOPES]);
        $googleClient->setSubject(CALENDAR_ID);
        $calendarService = new Google_Service_Calendar($googleClient);

        $createdEvent = $calendarService->events->get(CALENDAR_ID, $eventId);

        $createdEvent->setStart($startsAt);
        $createdEvent->setEnd($endsAt);

        $updatedEvent = $calendarService->events->update(CALENDAR_ID, $createdEvent->getId(), $createdEvent);
    }



    public function eventDelete($eventId)
    {
        $googleClient = new Google_Client();
        $googleClient->setApplicationName("Google Calendar");
        $googleClient->setAuthConfig(CREDENTIALS_PATH);
        $googleClient->setScopes([SCOPES]);
        $googleClient->setSubject(CALENDAR_ID);
        $calendarService = new Google_Service_Calendar($googleClient);

        $calendarService->events->delete(CALENDAR_ID, $eventId, array('sendUpdates' => 'all'));
    }


    public function deleteAttendee($attendeeEmail, $eventId) {
        $googleClient = new Google_Client();
        $googleClient->setApplicationName("Google Calendar");
        $googleClient->setAuthConfig(CREDENTIALS_PATH);
        $googleClient->setScopes([SCOPES]);
        $googleClient->setSubject(CALENDAR_ID);
        $calendarService = new Google_Service_Calendar($googleClient);

        $createdEvent = $calendarService->events->get(CALENDAR_ID, $eventId);
        $attendees = $createdEvent->getAttendees();
        $users = array();

        foreach ($attendees as $key => $value) {
            if($attendeeEmail != $value['email']) {
                array_push($users, array('email' => $value['email']));
            }
        }

        $createdEvent->setAttendees(
            $users
        );

        $updatedEvent = $calendarService->events->update(CALENDAR_ID, $createdEvent->getId(), $createdEvent);
    }
}