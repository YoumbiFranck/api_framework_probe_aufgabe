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


}