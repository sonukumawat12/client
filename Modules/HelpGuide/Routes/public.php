<?php 
// Route::prefix('helpguide')->group(function() {
//   Auth::routes([
//       'verify' => (boolean)setting('verify_email', true),
//       'register' => (boolean)setting('user_can_register', true),
//   ]);
// });

// if(isOAuthEnabled()){
//   Route::prefix('helpguide')->group(function() {
//     Route::get('login/{provider}', 'Auth\LoginController@redirectToProvider')
//     ->where(['provider' => implode('|', OAuthProviders())])
//     ->name('auth.socialite');
  
//     Route::get('login/{provider}/callback', 'Auth\LoginController@handleProviderCallback')
//     ->where(['provider' => implode('|', OAuthProviders())]);
//   });
// }