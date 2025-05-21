<?php

namespace App\Controller;

use App\Controller\BaseController;
use App\Database\Repositories\EventRepository;

class EventController extends BaseController
{
    private  EventRepository $eventRepository;
    public function __construct()
    {
        parent::__construct();
        $this->eventRepository = new EventRepository();
    }

    public function createEvent(): void
    {
        $creator_id = $this->input_handler->queryParams()["creator_id"] ?? null;
        if(empty($creator_id) || !is_numeric($creator_id)){
            respondError(400, 'creator_id is missing or invalid');
            return;
        }

        $input = $this->input_handler->bodyParams();

        // Check if the required fields are present
        $title = trim($input["title"] ?? '');
        $start_time = $input["start_time"] ?? '';
        $end_time = $input["end_time"] ?? '';
        $participants = $input["user_id"] ?? [];
        $description = $input["description"] ?? '';


        if(empty($title)){
            respondError(400, 'title is missing');
            return;
        }
        if(empty($start_time)){
            respondError(400, 'start_time is missing');
            return;
        }
        if(empty($end_time)){
            respondError(400, 'end_time is missing');
            return;
        }
        if(empty($participants) || !is_array($participants)){
            respondError(400, 'participants is missing or is not an array');
            return;
        }

        //check start_time < end_time
        if(strtotime($start_time) >= strtotime($end_time)){
            respondError(400, 'start_time must be before end_time');
            return;
        }

        //check if the creator_id exists
        if(!$this->eventRepository->userExists((int)$creator_id)){
            respondError(400, 'creator_id does not exist');
            return;
        }

        //check if the participants exist
        foreach($participants as $participant_id){
            if(!$this->eventRepository->userExists((int)$participant_id)){
                respondError(400, "participant user does not exist: $participant_id");
                return;
            }
        }

        // check file
        $attachment_path = null;
        if(!empty($_FILES["attachment"]) && $_FILES["attachment"]["error"] === UPLOAD_ERR_OK){
            $file_tmp = $_FILES["attachment"]["tmp_name"];
            $file_ext = strtolower(pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION));
            if($file_ext !== 'pdf'){
                respondError(400, "Only PDF attachments are allowed");
                return;
            }
        }

        // Create the event
        $event_id = $this->eventRepository->createEvent(
            (int)$creator_id,
            $title,
            $description,
            $start_time,
            $end_time,
        );

        if(!$event_id){
            respondError(500, "Event creation failed");
            return;
        }

        // Add participants to the event
        $success = $this->eventRepository->addParticipants($event_id, $participants);
        if(!$success){
            respondError(500, "Failed to add participants to the event");
            return;
        }

        // Handle file upload (if any)
        if(!empty($_FILES["attachment"]) && $_FILES["attachment"]["error"] === UPLOAD_ERR_OK){
            $attachment_path = dirname(__DIR__, 2) . "/attachments/{$event_id}_attachment.pdf";
            if(!move_uploaded_file($_FILES["attachment"]["tmp_name"], $attachment_path)){
                respondError(500, "Failed to move uploaded file");
                return;
            }
            $attachment_path = $this->getLastTwoParts($attachment_path);
            $this->eventRepository->addAttachment($event_id, $attachment_path);
        }

        // Respond with success
        respondSuccess([
            "message" => "Event created successfully",
            "event_id" => $event_id
        ]);


    }

    public function updateEvent(): void
    {
        $event_id = $this->input_handler->queryParams()["event_id"] ?? null;
        if(empty($event_id) || !is_numeric($event_id)){
            respondError(400, 'event_id is missing or invalid');
            return;
        }
        $event = $this->eventRepository->getEvent((int)$event_id); // check if event exists
        if(!$event){
            respondError(400, 'event_id does not exist');
            return;
        }

        $input = $this->input_handler->bodyParams();


        // Check fields to update are present
        $fields = [];
        if(isset($input["title"])){
            $fields["title"] = trim($input["title"]);
        }
        if(isset($input["description"])){
            $fields["description"] = trim($input["description"]);
        }
        if(isset($input["start_time"])){
            $fields["start_time"] = $input["start_time"];
        }
        if(isset($input["end_time"])){
            $fields["end_time"] = $input["end_time"];
        }
        if(isset($input["user_id"])){
            if(!is_array($input["user_id"])){
                respondError(400, 'user_id is not an array');
                return;
            }
            $fields["user_id"] = $input["user_id"];

            // add new participants to the event
            $add_result = $this->eventRepository->addParticipantsIfNotExists((int)$event_id, $input['user_id']);
            if(!$add_result){
                respondError(500, "Failed to add participants to the event");
                return;
            }

        }

        if(empty($fields)){
            respondError(400, 'No fields to update');
            return;
        }

        // creator id and create_at are not allowed to be changed
        if(isset($input["creator_id"]) || isset($input["created_at"])){
            respondError(400, 'creator_id and created_at cannot be updated');
            return;
        }


        $success = $this->eventRepository->updateEventFields((int)$event_id, $fields);
        if(!$success){
            respondError(500, "Failed to update event");
            return;
        }



        // update attachment
        if(!empty($_FILES["attachment"]) && $_FILES["attachment"]["error"] === UPLOAD_ERR_OK){
            $file_ext = strtolower(pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION));
            if($file_ext !== 'pdf'){
                respondError(400, "Only PDF attachments are allowed");
                return;
            }
            $attachment_path = dirname(__DIR__, 2) . "/attachments/{$event_id}_attachment.pdf";
            $replace_result = $this->eventRepository->replaceAttachment((int)$event_id, $attachment_path);
            if(!$replace_result){
                respondError(500, "Failed to replace attachment");
                return;
            }
        }elseif (array_key_exists("attachment", $input) && empty($input["attachment"])){
            //delete the attachment
            $delete_result = $this->eventRepository->deleteAttachment((int)$event_id);
            if(!$delete_result){
                respondError(500, "Failed to delete attachment");
                return;
            }
        }

        respondSuccess(['message' => 'Event updated successfully']);

    }

    // Termin anhang user_id anzeigen
    public function getEvent(): void
    {
        $event_id = $this->input_handler->queryParams()["event_id"] ?? null;
        $user_id = $this->input_handler->queryParams()["user_id"] ?? null;
        if((empty($event_id) || !is_numeric($event_id)) || (empty($user_id) || !is_numeric($user_id))){
            respondError(400, 'event_id or user_id is missing or invalid');
            return;
        }

        //check if event exists
        if(!$this->eventRepository->eventExists((int)$event_id)){
            respondError(400, 'event_id does not exist');
            return;
        }

        //check if user exists
        if(!$this->eventRepository->userExists((int)$user_id)){
            respondError(400, 'user_id does not exist');
            return;
        }

    }

    public function deleteEvent(): void
    {
        $input = $this->input_handler->bodyParams();
        $event_id = $input["event_id"] ?? null;

        if(empty($event_id) || !is_numeric($event_id)){
            respondError(400, 'event_id is missing or invalid');
            return;
        }

        //check if event exists
        if(!$this->eventRepository->eventExists((int)$event_id)){
            respondError(400, 'event_id does not exist');
            return;
        }

        //delete event
        $success = $this->eventRepository->deleteEvent((int)$event_id);
        if(!$success){
            respondError(500, "Failed to delete event");
            return;
        }

        // Respond with success
        respondSuccess([
            "message" => "Event deleted successfully",
            "event_id" => $event_id
        ]);
    }


    // get the last two parts of the path
    private function getLastTwoParts(string $path): string
    {
        $normalizedPath = str_replace('\\', '/', $path);

        $parts = explode('/', $normalizedPath);

        if (count($parts) >= 2) {
            return implode('/', array_slice($parts, -2, 2));
        }

        return $path;
    }





}