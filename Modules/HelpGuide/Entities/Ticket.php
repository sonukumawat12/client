<?php

namespace Modules\HelpGuide\Entities;

use Modules\HelpGuide\Traits\HasMeta;
use Illuminate\Database\Eloquent\Model;
// use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
// use Spatie\MediaLibrary\HasMedia;
// use Spatie\MediaLibrary\InteractsWithMedia;

use Modules\HelpGuide\Entities\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Ticket extends Model
{
    use SoftDeletes, HasMeta;
    
    public function user()
    {
        return $this->belongsTo('Modules\HelpGuide\Entities\User');
    }

    public function assignedTo()
    {
        return $this->belongsTo('Modules\HelpGuide\Entities\User','assigned_to','id');
    }

    public function category()
    {
        return $this->belongsTo('Modules\HelpGuide\Entities\Category');
    }

    public function conversations()
    {
        return $this->hasMany('Modules\HelpGuide\Entities\TicketConversation');
    }

    public function latestReply() {
        return $this->hasOne('Modules\HelpGuide\Entities\TicketConversation')->latest();
    }

    public static function categories()
    {
        return Category::select('id','name', 'parent_id','thumbnail')->where('has_ticket', 1)->orderBy('id', 'desc')->get();
    } 

    public function attachments()
    {
        // $mediaItems = $this->getMedia('attachments');
        $mediaItems = [];
        $attachments = [];
        foreach($mediaItems  as $file){
            $attachments[] = array('url' => asset($file->getUrl()), 'file_type'=> $file->mime_type);
        }
        return $attachments;
    }

    public static function assignTo(){
      
        $defaultAgent = setting('ticket_default_agent', null );
        $defaultAgent = User::find($defaultAgent);

        if( $defaultAgent ) return $defaultAgent->id;

        $agent = User::select(DB::raw("users.id, count(tickets.id) as 'tickets_count'"))
            // ->role(['agent','non-restricted_agent'])
            ->where('users.id', '!=', Auth::user()->id)
            ->leftJoin('tickets', 'users.id', 'tickets.assigned_to')
            ->groupBy('id')
            ->orderBy('tickets_count', 'ASC')
            ->first();

        return $agent ? $agent->id : false;
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('own_ticket', function($query) {
            $query->where('tickets.user_id', Auth::user()->id)->orWhere('tickets.assigned_to', Auth::user()->id);
        });
    }

    public static function overview()
    {
      $select = [
        DB::raw("IFNULL(SUM(status = 'open'), 0) AS open"),
        DB::raw("IFNULL(SUM(status = 'resolved'), 0) AS resolved"),
        DB::raw("IFNULL(SUM(status = 'closed'), 0) AS closed"),
        DB::raw('count(*) as total'),
      ];

      $tickets = Ticket::select($select);

      if (Auth::User()->can('statistics_view_any')) {
          $tickets->withoutGlobalScope('own_ticket');
      }

      return $tickets->first();
    }
}
