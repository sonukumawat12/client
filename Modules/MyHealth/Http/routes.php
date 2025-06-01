<?php

Route::group(['middleware' => ['web','tenant.context'], 'prefix' => 'my-health', 'namespace' => 'Modules\MyHealth\Http\Controllers'], function () {
   Route::resource('pharmacy-products', 'PharmacyProductController');
   Route::get('prescription/getPrescriptions/{patient_id}', 'PrescriptionController@getPrescriptions');
   Route::get('patient/getPatient', 'PatientController@getPatient');
   Route::get('patient/sugar_reading', 'SugerReadingController@index');
   Route::get('patient/sugar_reading/{id}/edit', 'SugerReadingController@edit');
   Route::get('patient/sugar_reading_create/{id}/add', 'SugerReadingController@add');
   Route::get('patient/sugar_reading/fetchData', 'SugerReadingController@fetchData');
   Route::post('patient/sugar_reading/{id}/update', 'SugerReadingController@update');
   Route::get('patient/sugar_readings', 'SugerReadingController@create');
   Route::post('patient/sugar_read', 'SugerReadingController@store');
   Route::resource('patient-payments', 'PatientPaymentController');
   Route::resource('patient', 'PatientController');
//   Route::resource('medication', 'MedicationController');
   Route::post('medicine-upload', 'MedicineController@upload');
        Route::resource('medicine', 'MedicineController');
    
         Route::get('medication', 'MedicationController@index')->name('medication.index');
         
         Route::post('medication', 'MedicationController@index')->name('medication.index');
    Route::get('medication/create', 'MedicationController@create')->name('medication.create');
    
    Route::get('medication/store', 'MedicationController@store')->name('medication.store');
    Route::post('medication/store', 'MedicationController@store');
    
    
    Route::get('prescription-enterAmount/{id}', 'PrescriptionController@enterAmount');
    

    Route::post('prescription-updateAmount', 'PrescriptionController@updateAmount');

    Route::post('prescription-upload', 'PrescriptionController@upload');

    Route::get('image-modal', 'PrescriptionController@imageModal');

    Route::resource('prescription', 'PrescriptionController');
    
    Route::get('test-enterAmount/{id}', 'TestController@enterAmount');

    Route::post('test-updateAmount', 'TestController@updateAmount');

    Route::post('test-upload', 'TestController@upload');

    Route::resource('test', 'TestController');
    
    
    
    
    Route::resource('hospital', 'HospitalController');

    Route::post('get_doctor', 'DoctorController@getDocorts');

    Route::resource('doctor', 'DoctorController');
    
    Route::resource('allergies', 'AllergiesController');
});
