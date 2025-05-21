<?php

namespace App\Database\Repositories;
use Illuminate\Database\Capsule\Manager as DB;

class EventRepository
{

    //check if user exists
    public function userExists(int $user_id): bool
    {
        return DB::table('users')
            ->where('id', $user_id)->exists();
    }

    //check if event exists
    public function eventExists(int $event_id): bool
    {
        return DB::table('events')
            ->where('id', $event_id)->exists();
    }



    //create event
    public function createEvent(int $creator_id, string $title, ?string $description, string $start_time, string $end_time): ?int
    {
        try{
            return DB::table('events')->insertGetId([
                'creator_id' => $creator_id,
                'title' => $title,
                'description' => $description,
                'start_time' => $start_time,
                'end_time' => $end_time,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }catch (\Exception $e){
            // Log the error message
            error_log("Error creating event: " . $e->getMessage());
            return null;
        }
    }

    //upadte event
    public function updateEventFields(int $event_id, array $fields): bool
    {
        $fields['updated_at'] = date('Y-m-d H:i:s');
        // entferne die Felder, die nicht in der Tabelle events existieren
        $fields = array_intersect_key($fields, array_flip(['title', 'description', 'start_time', 'end_time', 'updated_at']));
        try{
            return DB::table('events')
                ->where('id', $event_id)
                ->update($fields) > 0;
        }catch (\Exception $e){
            // Log the error message
            error_log("Error updating event: " . $e->getMessage());
            return false;
        }
    }

    // add participants to event if not already added (update)
    public function addParticipantsIfNotExists(int $event_id, array $user_ids): bool
    {
        $errors = [];
        foreach($user_ids as $user_id){
            // check if the user exists
            $exists = DB::table('users')
                ->where('id', $user_id)
                ->exists();
            if(!$exists){
                $errors[] = "User $user_id not found";
                continue;
            }

            // check if the user is already a participant
            $alreadyParticipant = DB::table('event_participants')
                ->where('event_id', $event_id)
                ->where('user_id', $user_id)
                ->exists();
            if(!$alreadyParticipant){
                try{
                    DB::table('event_participants')->insert([
                        'event_id' => $event_id,
                        'user_id' => $user_id,
                        'status' => 'invited',
                        'invited_at' => date('Y-m-d H:i:s'),
                    ]);
                }catch (\Exception $e){
                    // Log the error message
                    $errors[] = "Error with user $user_id: " . $e->getMessage();
                    continue;
                }
            }
        }
        if (!empty($errors)) {
            foreach ($errors as $error) {
                error_log($error);
            }
            return false;
        }
        return true; // all participants added successfully
    }

    //get attachments
    public function getAttachment(int $event_id): ?object
    {
        return DB::table('attachments')
                    ->where('event_id', $event_id)
                    ->first();
    }

    public function deleteAttachment(int $event_id): bool
    {
      $attachment = $this->getAttachment($event_id);
      if($attachment){
              $file_path = dirname(__DIR__, 3) . '/' . $attachment->file_path;
              $file_path = str_replace("\\", "/", $file_path); // Convert backslashes to forward slashes
              # echo "Deleting file: " . $file_path . "\n"; check if file exists
              if (file_exists($file_path)) {
                  unlink($file_path);
              }
          try{
              return DB::table('attachments')
                  ->where('event_id', $event_id)
                  ->delete();
          }catch (\Exception $e){
                // Log the error message
                error_log("Error deleting attachment: " . $e->getMessage());
                return false;
          }

      }
        return true;
    }

    //replace attachment
    public function replaceAttachment(int $event_id, string $tmp_file): bool
    {
        // delete the old attachment
        $delete_result = $this->deleteAttachment($event_id);
        if(!$delete_result){
            error_log("Error deleting attachment" );
            return false;
        }
        // insert the new attachment
        $destination_path = dirname(__DIR__, 3) . "/attachments/{$event_id}_attachment.pdf";
        $destination_path = str_replace("\\", "/", $destination_path); // Convert backslashes to forward slashes
        if (!move_uploaded_file($tmp_file, $destination_path)) {
            error_log("Error moving uploaded file");
            return false;
        }
        // save the new attachment to the database
        try{
             DB::table('attachments')->insert([
                'event_id' => $event_id,
                'file_path' => $destination_path,
                'uploaded_at' => date('Y-m-d H:i:s'),
            ]);
        }catch (\Exception $e){
            // Log the error message
            error_log("Error adding attachment: " . $e->getMessage());
            return false;
        }

        return true;
    }

    //add participants to event
    public function addParticipants(int $event_id, array $user_ids): bool
    {
        $rows = [];
        foreach ($user_ids as $user_id) {
            $rows[] = [
                'event_id' => $event_id,
                'user_id' => $user_id,
                'status' => 'invited',
                'invited_at' => date('Y-m-d H:i:s'),
            ];
        }
        try{
            return DB::table('event_participants')->insert($rows);
        }catch (\Exception $e){
            // Log the error message
            error_log("Error adding participants: " . $e->getMessage());
            return false;
        }
    }

    // add attachments to event
    public function addAttachment(int $event_id, string $file_path): bool
    {
        try{
            return DB::table('attachments')->insert([
                'event_id' => $event_id,
                'file_path' => $file_path,
                'uploaded_at' => date('Y-m-d H:i:s'),
            ]);
        }catch (\Exception $e){
            // Log the error message
            error_log("Error adding attachment: " . $e->getMessage());
            return false;
        }

    }

 public function deleteEvent(int $event_id): bool
    {
        try{
            // get all attachments
            $attachments = DB::table('attachments')
                ->where('event_id', $event_id)
                ->get();

            // delete event (OnDelete CASCADE will delete all attachments)
            $deleted = DB::table('events')
                ->where('id', $event_id)
                ->delete();

            // delete file on the server
            foreach ($attachments as $attachment) {
                $file_path = dirname(__DIR__, 3) . '/' . $attachment->file_path;
                $file_path = str_replace("\\", "/", $file_path); // Convert backslashes to forward slashes
                # echo "Deleting file: " . $file_path . "\n"; check if file exists
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
            return $deleted > 0;

        }catch (\Exception $e){
            // Log the error message
            error_log("Error deleting event: " . $e->getMessage());
            return false;
        }
    }


    // get all events
    public function getEventsByUserId(int $user_id): ?array
    {
        $user = DB::table('users')
            ->select('id', 'username', 'email')
            ->where('id', $user_id)
            ->first();
        if (!$user) {
            return null;
        }
        $events = DB::table('events')
            ->where('creator_id', $user_id)
            ->get();

        $eventData = [];
        foreach ($events as $event) {
            $participants = DB::table('event_participants as ep')
                ->join('users as u', 'ep.user_id', '=', 'u.id')
                ->where('ep.event_id', $event->id)
                ->get();

            $attachments = DB::table('attachments')
                ->where('event_id', $event->id)
                ->select('id', 'file_path', 'uploaded_at')
                ->get();

            $creator = DB::table('users')
                ->where('id', $event->creator_id)
                ->select('id', 'username', 'email')
                ->first();

            $eventData[] = [
                'id' => $event->id,
                'title' => $event->title,
                'description' => $event->description,
                'start_time' => $event->start_time,
                'end_time' => $event->end_time,
                'created_at' => $event->created_at,
                'creator' => $creator,
                'participants' => $participants,
                'attachments' => $attachments,
            ];
        }

        return [
            'user' => $user,
            'events' => $eventData,
        ];

    }

    public function getEventsByEventId(int $event_id): ?array
    {
        $events = DB::table('events')
            ->where('id', $event_id)
            ->get();

        $eventData = [];
        foreach ($events as $event) {
            $participants = DB::table('event_participants as ep')
                ->join('users as u', 'ep.user_id', '=', 'u.id')
                ->where('ep.event_id', $event->id)
                ->get();

            $attachments = DB::table('attachments')
                ->where('event_id', $event->id)
                ->select('id', 'file_path', 'uploaded_at')
                ->get();

            $creator = DB::table('users')
                ->where('id', $event->creator_id)
                ->select('id', 'username', 'email')
                ->first();

            $eventData[] = [
                'id' => $event->id,
                'title' => $event->title,
                'description' => $event->description,
                'start_time' => $event->start_time,
                'end_time' => $event->end_time,
                'created_at' => $event->created_at,
                'creator' => $creator,
                'participants' => $participants,
                'attachments' => $attachments,
            ];
        }

        return [
            'events' => $eventData,
        ];

    }


    // get an event
    public function getEvent(int $event_id): ?object
    {
        try{
            return DB::table('events')
                ->where('id', $event_id)
                ->first();
        }catch (\Exception $e){
            // Log the error message
            error_log("Error getting event: " . $e->getMessage());
            return null;
        }
    }


}