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