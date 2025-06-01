<?php

namespace Modules\HelpGuide\Http\Controllers\Dashboard;


use Modules\HelpGuide\Entities\User;
use Modules\HelpGuide\Entities\Ticket;
use Modules\HelpGuide\Entities\Article;
use Modules\HelpGuide\Entities\Category;
use Modules\HelpGuide\Entities\ArticleTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Modules\HelpGuide\Http\Controllers\Controller;

class SearchController extends Controller
{
      
  public function search(Request $request)
  {
      $query = $request->input('q');
      $type = $request->input('type');
      return response()->json([
          'status' => 'ok',
          'results' => $this->doSearch($query, $type)
      ], 201);
  }

  private function doSearch($q, $type)
  {
      $types = explode(',', $type);
      $results = ["total" => 0];

      $currentUser = Auth::user();

      if(in_array('articles',  $types)){
          $articles = $this->articles($q);
          $results['articles'] = $articles;
          $results['total'] += count($articles);
      }

      if(in_array('tickets',  $types)){
          $tickets = $this->tickets($q);
          $results['tickets'] = $tickets;
          $results['total'] += count($tickets);
      }

      if(in_array('categories',  $types) && $currentUser->can('manage_categories')){
          $categories = $this->categories($q);
          $results['categories'] = $categories;
          $results['total'] += count($categories);
      }

      if(in_array('customers',  $types) && $currentUser->can('manage_customers')){
          $customers = $this->customers($q);
          $results['customers'] = $customers;
          $results['total'] += count($customers);
      }

      if(in_array('employees',  $types) && $currentUser->can('manage_employees')){
        $employees = $this->employees($q);
        $results['employees'] = $employees;
        $results['total'] += count($employees);
    }

      return $results;
  }
  
  private function articles($q)
  {
      $articles = ArticleTranslation::select('id','title','article_id')
          ->where('article_id', (int)$q)
          ->orWhere('title','LIKE','%'.$q.'%')
          ->orWhere('content','LIKE','%'.$q.'%')
          ->orWhere(function ($query) use ($q) {
              $query->whereHas('article', function ($query) use($q) {
                  $query->whereHas('tags', function ($query) use($q) {
                      $query->select('article_id')->where('name','LIKE', '%'.$q.'%');
                  });
              });
          });

      // If can manage articles show unpublished articles
      if(!Auth::guest() && Auth::user()->can('manage_articles')){
          $articles->withoutGlobalScope('published');
      }

      $articles = $articles->get(15);

      $articles = $articles->map(function($item){
          $item['url'] = $item->url; 
          return $item;
      });

      return $articles;
  }

  private function customers($term)
  {
      return User::select('id', 'first_name', 'email')
          ->where(function($q) use ($term){
              $q->where('users.id', (int)$term)
              ->orWhere('users.first_name','like', '%'.$term.'%')
              ->orWhere('users.email','like', '%'.$term.'%');
          })
          ->get()->map->format();
  }

  private function employees($term)
  {
      return User::select('id', 'first_name', 'email')
          ->where(function($q) use ($term){
              $q->where('users.id', (int)$term)
              ->orWhere('users.first_name','like', '%'.$term.'%')
              ->orWhere('users.email','like', '%'.$term.'%');
          })
          ->get()->map->format();
  }

  private function categories($q)
  {
      return Category::select('id', 'name')
              ->where('id', (int)$q)
              ->orWhere('name','like', '%'.$q.'%')
              ->get();
  }

  private function tickets($q)
  {
      $tickets = Ticket::select('id', 'title')
              ->where('id', (int)$q)
              ->orWhere('title','like', '%'.$q.'%')
              ->orWhere(function ($query) use ($q) {
                  $query->whereHas('conversations', function ($query) use($q) {
                      $query->select('ticket_id')->where('content','LIKE', '%'.$q.'%');
                  });
              });

      // If can view any ticket remove own_ticket scope
      if(!Auth::guest() && Auth::user()->can('view_any_ticket')){
          $tickets->withoutGlobalScope('own_ticket');
      }

      return $tickets->get();
  }
}
