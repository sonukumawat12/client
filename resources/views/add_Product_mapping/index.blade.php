@extends('layouts.app')
@section('title', __('Add Supplier Map Products'))

@section('content')

<!-- Content Header (Page header) -->
@php
    $form_id = 'contact_add_forms';
    
    if(isset($quick_add)){
    $form_id = 'quick_add_contact';
    }
@endphp 
  <style>
      #ss_imp_list {
  max-height: 200px; /* Set the maximum height of the list box */
  overflow-y: auto; /* Enable vertical scrolling */
}
  #ss_unimp_list {
  max-height: 200px; /* Set the maximum height of the list box */
  overflow-y: auto; /* Enable vertical scrolling */
}
  .listbox-area {
  display: grid;
  grid-gap: 2em;
  grid-template-columns: repeat(2, 1fr);
  padding: 20px;
  border: 1px solid #aaa;
  margin-top:30px;
  border-radius: 4px;
  background: #fff;
}

[role="listbox"] {
  margin: 1em 0 0;
  padding: 0;
  min-height: 18em;
  border: 1px solid #aaa;
  background: white;
}

[role="listbox"]#ss_elem_list {
  position: relative;
  max-height: 18em;
  overflow-y: auto;
}

[role="listbox"] + *,
.listbox-label + * {
  margin-top: 1em;
}

[role="group"] {
  margin: 0;
  padding: 0;
}

[role="group"] > [role="presentation"] {
  display: block;
  margin: 0;
  padding: 0 0.5em;
  font-weight: bold;
  line-height: 2;
  background-color: #ccc;
}

[role="option"] {
  position: relative;
  display: block;
  margin: 2px;
  padding: 2px 1em 2px 1.5em;
  line-height: 1.8em;
  cursor: pointer;
}

[role="listbox"]:focus [role="option"].focused {
  background: #bde4ff;
}

[role="listbox"]:focus [role="option"].focused,
[role="option"]:hover {
  outline: 2px solid currentcolor;
}

.move-right-btn span.checkmark::after {
  content: " →";
}

.move-left-btn span.checkmark::before {
  content: "← ";
}

[role="option"][aria-selected="true"] span.checkmark::before {
  position: absolute;
  left: 0.5em;
  content: "✓";
}

button[aria-haspopup="listbox"] {
  position: relative;
  padding: 5px 10px;
  width: 150px;
  border-radius: 0;
  text-align: left;
}

button[aria-haspopup="listbox"]::after {
  position: absolute;
  right: 5px;
  top: 10px;
  width: 0;
  height: 0;
  border: 8px solid transparent;
  border-top-color: currentcolor;
  border-bottom: 0;
  content: "";
}

button[aria-haspopup="listbox"][aria-expanded="true"]::after {
  position: absolute;
  right: 5px;
  top: 10px;
  width: 0;
  height: 0;
  border: 8px solid transparent;
  border-top: 0;
  border-bottom-color: currentcolor;
  content: "";
}

button[aria-haspopup="listbox"] + [role="listbox"] {
  position: absolute;
  margin: 0;
  width: 9.5em;
  max-height: 10em;
  border-top: 0;
  overflow-y: auto;
}

[role="toolbar"] {
  display: flex;
}

[role="toolbar"] > * {
  border: 1px solid #aaa;
  background: #ccc;
}

[role="toolbar"] > [aria-disabled="false"]:focus {
  background-color: #eee;
}

button {
  font-size: inherit;
}

button[aria-disabled="true"] {
  opacity: 0.5;
}

.annotate {
  color: #366ed4;
  font-style: italic;
}

.hidden {
  display: none;
}

.offscreen {
  position: absolute;
  width: 1px;
  height: 1px;
  overflow: hidden;
  clip: rect(1px 1px 1px 1px);
  clip: rect(1px, 1px, 1px, 1px);
  font-size: 14px;
  white-space: nowrap;
}
  </style> 
<style>
.select2-results__option .wrap:before{
    font-family:fontAwesome;
    color:#999;
    content:"\f096";
    width:25px;
    height:25px;
    padding-right: 10px;
    
}
.select2-results__option[aria-selected=true] .wrap:before{
    content:"\f14a";
}

/* not required css */

.row
{
  padding: 10px;
}

.select2-multiple, .select2-multiple2
{
  width: 50%
}
.list-box {
list-style-type: none;
}

.checkbox-container {
display: block;
position: relative;
padding-left: 25px;
margin-bottom: 5px;
cursor: pointer;
}

.checkbox-container .checkmark {
position: absolute;
top: 0;
left: 0;
height: 18px;
width: 18px;
border: 1px solid #ccc;
background-color: #fff;
}

.checkbox-container input[type="checkbox"] {
position: absolute;
opacity: 0;
cursor: pointer;
}

.checkbox-container .checkmark:after {
content: "";
position: absolute;
display: none;
}

.checkbox-container input:checked + .checkmark:after {
display: block;
}

.checkbox-container .checkmark:after {
left: 6px;
top: 2px;
width: 5px;
height: 10px;
border: solid #000;
border-width: 0 2px 2px 0;
transform: rotate(45deg);
}
.select2-container .select2-selection--single {
    width: 250px;
}

 
.popup{
   
    cursor: pointer
}
.popupshow{
    z-index: 99999;
    display: none;
}
.popupshow .overlay{
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,.66);
    position: absolute;
    top: 0;
    left: 0;
}

.popupshow .img-show{
        width: 900px;
    height: 600px;
    background: #FFF;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%,-50%);
    overflow: hidden;
}
.img-show span{
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 99;
    cursor: pointer;
}
.img-show img{
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
}
.form-group select {
    width: 100%;
}
 .red-hover:hover option {
        background-color: red;
        color: white;
    }
/*End style*/

</style>

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">Contacts</h4>
                <ul class="breadcrumbs pull-left" style="margin-top: 15px">
                    <li><a href="#">Product</a></li>
                    <li><span>Add Supplier Map Products</span></li>
                </ul>
            </div>
        </div>
    </div>
</div>

     
    <!-- Main content -->
    <section class="content main-content-inner">
        
        <div class="row">
             {!! Form::open(['url' => action('SupplierMappingController@store'), 'method' => 'post', 'id' => $form_id,'enctype'=>"multipart/form-data",'files' => true ]) !!}
    
            <div class="col-sm-12">
                @component('components.filters', ['title' => __('report.filters')])
                    
                    <div class="form-group col-sm-3 form-inline" >
                       <label for="type">Supplier List</label>
                       <br>
                        <div class="form-group">
                      
                        
                        {!! Form::select('type', $name, !empty($type) ? $type : null , ['class' => 'form-control select2', 'id' =>
                        'type','placeholder'
                        => __('messages.please_select'), 'required','closeOnSelect:false' ,'style' => 'width: 50%;']); !!}
                        </div>
                     </div>
                   
                    <div class="form-group col-sm-8 form-inline" >
                          
                <div class="listbox-area">
                  <div class="left-area">
                    <span id="ss_imp_l" class="listbox-label" style="color:red;">Unmapped Products:</span>
                	<br>
                	  <input type="text" id="searchInput" oninput="filterList()" placeholder="Search items" style="width:100%;">
                    <ul id="ss_imp_list" tabindex="0" role="listbox" aria-labelledby="ss_imp_l" aria-multiselectable="true">
                 
                    </ul>
                    <div role="toolbar" aria-label="Actions" class="toolbar">
                      <button type="button" id="ex1-up" class="toolbar-item selected" aria-keyshortcuts="Alt+ArrowUp" aria-disabled="true" style="display:none">
                        Up
                      </button>
                      <button type="button" id="ex1-down" class="toolbar-item" tabindex="-1" aria-keyshortcuts="Alt+ArrowDown" aria-disabled="true" style="display:none">
                        Down
                      </button>
                      <button type="button" id="ex1-delete" class="toolbar-item move-right-btn" tabindex="-1" aria-keyshortcuts="Alt+ArrowRight Delete" aria-disabled="true">
                        <span class="checkmark" aria-hidden="true"></span>
                      Mapped
                    </div>
                  </div>
                  <div class="right-area">
                    <span id="ss_unimp_l" class="listbox-label"  style="color:blue;">Mapped Products:</span>
                	<br>
                	  <input type="text" id="searchInputMapped" oninput="filterListmapped()" placeholder="Search items" style="width:100%;">
                    <ul id="ss_unimp_list" tabindex="0" role="listbox" aria-labelledby="ss_unimp_l" aria-activedescendant="" aria-multiselectable="true"></ul>
                    <button type="button" id="ex1-add" class="move-left-btn" aria-keyshortcuts="Alt+ArrowLeft Enter" aria-disabled="true">
                      <span class="checkmark" aria-hidden="true"></span>
                      Unmapped
                    </button>
                  </div>
                  <div class="offscreen">Last change: <span aria-live="polite" id="ss_live_region"></span></div>
            </div>		
        </div>
           
    </div>  
</div>
@endcomponent
      
             <div class="col-sm-12">
                    <div class="box-tools pull-right">
                          <p class="text-muted">
                     
                      <!--   <input type="hidden" id="default_contact_id" value="{{ $contact_id ?? ''}}" > -->
                         <button type="button" id="save-mapped" class="btn btn-primary">@lang('messages.save')</button></p>
                </div> 
                
          </div>        
</div>   
{!! Form::close() !!}
    </div>
         
    <input type="hidden" value="{{$type}}" id="contact_type">
    @component('components.widget', ['class' => 'box-primary', 'title' => __( '', ['contacts' =>
    __('lang_v1.'.$type.'s') ])]) 
    @endcomponent
    
    <div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade pay_contact_due_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    
    </section>

<div class="popupshow">
  <div class="overlay"></div>
  <div class="img-show">
    <span>X</span>
    <img src="">
  </div>
</div>
 @push('css')
 
@endpush
<!-- /.content -->

@endsection

@section('javascript')

@if(session('status'))
    @if(session('status')['success'])
        <script>
            toastr.success('{{ session("status")["msg"] }}');
        </script>
    @else
        <script>
            toastr.error('{{ session("status")["msg"] }}');
        </script>
    @endif
@endif
@push('scripts')
<script>
   $(document).ready(function() {
       $('#product_id').select2({
         var list = $('#product_id').select2({
    closeOnSelect: false
}).on('select2:closing', function(e) {
    e.preventDefault();
}).on('select2:closed', function(e) {
    list.select2('open');
});
list.select2('open');

});
    });
</script>

@endpush 
<script>   
        $(document).ready(function() {
            
              $('#save-mapped').on('click', function() {
    // Retrieve the list items and send them to the server
    sendListItemsToServer();
  });
             function sendListItemsToServer() {
                 
                  let itemsToDelete;
                     var listItems = $('#ss_unimp_list');
                    var ulElement = document.getElementById("ss_unimp_list");
                    
                    // Loop through each <li> element inside the <ul>
                    for (var i = 0; i < ulElement.children.length; i++) {
                    var liElement = ulElement.children[i];
                    
                    // Extract the value of each <li> element
                    var liValue = liElement.innerHTML;
                    
                    // Do something with the value (e.g. add it to an array)
                    console.log("List item for text"+liValue);
                    }
                    var itemsToDeletemapped;
                  itemsToDelete=  Array.from(listItems.find('li')).map(item => item.id);
                    console.log("List items array: ", itemsToDelete);
                    var data = {
                    type: $('#type').val(),
                       ss_imp_list: itemsToDelete,
                    ss_umimp_list :itemsToDeletemapped
                    }; 
                      
                     
                    // Send the data to the server using AJAX
                    $.ajax({
                        url: '/contacts/create_mappings',
                        type: 'GET',
                        data: data,
                        success: function(response) {
                             if (response.success) {
          toastr.success('Saved successful');
          
          // Reload the page after successful submission
           
        }
                            // Handle the server response here
                            },
                        error: function(xhr, status, error) {
                        // Handle any errors here
                        }
                        });
                    
                    
                 
             }
            
            $('#ex1-delete').on('click', function() {
                 /*    let itemsToDelete;
                     var listItems = $('#ss_imp_list');
                    var ulElement = document.getElementById("ss_imp_list");
                    
                    // Loop through each <li> element inside the <ul>
                    for (var i = 0; i < ulElement.children.length; i++) {
                    var liElement = ulElement.children[i];
                    
                    // Extract the value of each <li> element
                    var liValue = liElement.innerHTML;
                    
                    // Do something with the value (e.g. add it to an array)
                    console.log("List item for array"+liValue);
                    }
                  itemsToDelete=  Array.from(listItems.find('li')).map(item => item.id);
                    console.log("List items: ", itemsToDelete);
  
  
  
                    
                    var listItemsmapped = $('#ss_umimp_list');
               
                    let itemsToDeletemapped;
                    itemsToDelete = Array.from(listItems.find('[aria-selected="true"]')).map(item => item.id);
                    itemsToDeletemapped = Array.from(listItemsmapped.find('[aria-selected="true"]')).map(item => item.id);
                   // console.log("unmapped"+itemsToDeletemapped);
                    console.log("mapped"+itemsToDelete);
                    var data = {
                    type: $('#type').val(),
                    ss_imp_list: itemsToDelete,
                    ss_umimp_list :itemsToDeletemapped
                    };
                      
                     
                    // Send the data to the server using AJAX
                    $.ajax({
                        url: '/contacts/mapping',
                        type: 'POST',
                        data: data,
                        success: function(response) {
                            console.log(response);
                            // Handle the server response here
                            },
                        error: function(xhr, status, error) {
                        // Handle any errors here
                        }
                        });*/
                    }); 

                  /*  $('#ex1-add').on('click', function() {
                        var listItems = $('#ss_imp_list');
                        var listItemsmapped = $('#ss_unimp_list');
                        let itemsToDeletemapped;
                        let itemsToDelete;
                        // itemsToDelete = Array.from(listItems.find('[aria-selected="true"]')).map(item => item.id);
                        itemsToDeletemapped = Array.from(listItemsmapped.find('[aria-selected="true"]')).map(item => item.id);
                        console.log(itemsToDeletemapped);
                        var data = {
                        type: $('#type').val(),
                        ss_umimp_list :itemsToDeletemapped
                    };
  
                     
                // Send the data to the server using AJAX
                $.ajax({
                    url: '/contacts/mapping',
                    type: 'POST',
                    data: data,
                    success: function(response) {
                     //  location.reload();
                        // Handle the server response here
                    },
                    error: function(xhr, status, error) {
                        console.log("error")
                    }
                });
            }); */
        });
     
</script>
 
<script>
    $(document).ready(function() {
        $('#mapped').on('change', function() {
            var selectedValue = $(this).val();
            
            if (selectedValue !== '') {
                $('#right-arrow').show();
                $('#left-arrow').hide();
            } else {
                $('#right-arrow').hide();
                $('#left-arrow').show();
            }
        });
    });
</script>
<script>
    $(document).ready(function() {
        $('#skills').on('change', function() {
            var selectedValue = $(this).val();
            
            if (selectedValue !== '') {
                $('#right-arrow').hide();
                $('#left-arrow').show();
            } else {
                $('#right-arrow').show();
                $('#left-arrow').hide();
            }
        });
    });
</script>
<script>
    $(document).ready(function () {
        $('.js-example-basic-multiple').select2({ closeOnSelect: false });;
    });
</script>
<script>
var selectedItems = [];
$(document).ready(function() {
   

 
  $('forms').submit(function(event) {
    event.preventDefault(); // Prevent form submission

    var form = $(this);
    var formData = new FormData(form[0]);
   
    $.ajax({
      url: form.attr('action'),
      method: form.attr('method'),
      data: formData,
      processData: false,
      contentType: false,
      success: function(response) {
        // Handle the success response
        console.log(response);
        
        // Display success toast
        if (response.success) {
          toastr.success('Saved successful');
          
          // Reload the page after successful submission
          location.reload();
        } else {
          toastr.error('Product bind to supplier exist');
        }
        
        // Clear the form fields
        form[0].reset();
      },
      error: function(xhr, status, error) {
         
        toastr.error('Supplier product mapping failed');
        
        // Optionally, you can display a more detailed error message
        // toastr.error('Supplier product mapping failed: ' + error);
      }
    });
});

  
});

  $('#names').on('click', '.list-item', function(e) {
    e.preventDefault();
    var $this = $(this);

    // Toggle the selected class
    $this.toggleClass('selected');

    // Update the selectedItems array
    var itemId = $this.data('value');
    if ($this.hasClass('selected')) {
      // Add the item ID to the selectedItems array
      selectedItems.push(itemId);
    } else {
      // Remove the item ID from the selectedItems array
      var index = selectedItems.indexOf(itemId);
      if (index !== -1) {
        selectedItems.splice(index, 1);
      }
    }

    // Update the hidden input value
    $('#selected_name').val(selectedItems.join(','));

    // Prevent the click event from bubbling up to the parent elements
    return false;
  });
 if ($('#date_filter').length == 1) {
        $('#date_filter').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#stock_adjustment_date_filter span').html(
                    start.format(date_filter) + ' ~ ' + end.format(moment_date_format)
                );
               // updateStockAdjustmentReport();
            }
        );
        $('#date_filter').on('cancel.daterangepicker', function (ev, picker) {
            $('#purchase_sell_date_filter').html(
                '<i class="fa fa-calendar"></i> ' + LANG.filter_by_date
            );
        });
       // updateStockAdjustmentReport();
    }
 $(document).ready(function() {
     
         
      $('#type').on('change', function() {
      
        var selectedId = $(this).val();
        var list = document.getElementById("ss_unimp_list");
        list.innerHTML = "";  
        var listmapping = document.getElementById("ss_imp_list");
        listmapping.innerHTML = "";  
      $.ajax({
            method: 'get',
            url: '/contacts/add-supplier-map-product/get-supplier-mapped',
            data: {
                supplier_id: selectedId
            },
            success: function(result) {
                 
                var mappingNames = result.mappingnames;
                var names = result.names;
                //console.log(names);
              // Clear the previous list
           var listItems = Object.entries(mappingNames).map(function([id, text]) {
                  return {
                    id: id,
                    text: text
                  };
                });
            var listItemsmapping = Object.entries(names).map(function([id, text]) {
                  return {
                    id: id,
                    text: text
                  };
                });
        for (var i = 0; i < listItems.length; i++) {
          var listItem = document.createElement("li");
          listItem.setAttribute("id", listItems[i].id);
          listItem.setAttribute("role", "option");
          listItem.innerHTML = `
            <span class="checkmark" aria-hidden="true"></span>
            ${listItems[i].text}
          `;
          list.appendChild(listItem);
        }
                    
          for (var i = 0; i < listItemsmapping.length; i++) {
          var listItem = document.createElement("li");
          listItem.setAttribute("id", listItemsmapping[i].id);
          listItem.setAttribute("role", "option");
          listItem.innerHTML = `
            <span class="checkmark" aria-hidden="true"></span>
            ${listItemsmapping[i].text}
          `;
          listmapping.appendChild(listItem);
        }
              
            },
        }); 
});

    
});
 
</script>

      
<script>
  function filterList() {
      var searchInput = document.getElementById("searchInput");
      var filter = searchInput.value.toLowerCase();
      var listItems = document.querySelectorAll("#ss_imp_list li");

      for (var i = 0; i < listItems.length; i++) {
        var listItem = listItems[i];
        var text = listItem.innerText.toLowerCase();

        if (text.includes(filter)) {
          listItem.style.display = "block";
        } else {
          listItem.style.display = "none";
        }
      }
    }
	  function filterListmapped() {
      var searchInput = document.getElementById("searchInputMapped");
      var filter = searchInput.value.toLowerCase();
      var listItems = document.querySelectorAll("#ss_unimp_list li");

      for (var i = 0; i < listItems.length; i++) {
        var listItem = listItems[i];
        var text = listItem.innerText.toLowerCase();

        if (text.includes(filter)) {
          listItem.style.display = "block";
        } else {
          listItem.style.display = "none";
        }
      }
    }
/*
 *   This content is licensed according to the W3C Software License at
 *   https://www.w3.org/Consortium/Legal/2015/copyright-software-and-document
 */

'use strict';

/**
 * @namespace aria
 * @description
 * The aria namespace is used to support sharing class definitions between example files
 * without causing eslint errors for undefined classes
 */
var aria = aria || {};

/**
 * @class
 * @description
 *  Listbox object representing the state and interactions for a listbox widget
 * @param listboxNode
 *  The DOM node pointing to the listbox
 */

aria.Listbox = class Listbox {
  constructor(listboxNode) {
    this.listboxNode = listboxNode;
    this.activeDescendant = this.listboxNode.getAttribute(
      'aria-activedescendant'
    );
    this.multiselectable = this.listboxNode.hasAttribute(
      'aria-multiselectable'
    );
    this.moveUpDownEnabled = false;
    this.siblingList = null;
    this.startRangeIndex = 0;
    this.upButton = null;
    this.downButton = null;
    this.moveButton = null;
    this.keysSoFar = '';
    this.handleFocusChange = function () {};
    this.handleItemChange = function () {};
    this.registerEvents();
  }

  registerEvents() {
    this.listboxNode.addEventListener('focus', this.setupFocus.bind(this));
    this.listboxNode.addEventListener('keydown', this.checkKeyPress.bind(this));
    this.listboxNode.addEventListener('click', this.checkClickItem.bind(this));

    if (this.multiselectable) {
      this.listboxNode.addEventListener(
        'mousedown',
        this.checkMouseDown.bind(this)
      );
    }
  }

  setupFocus() {
    if (this.activeDescendant) {
      const listitem = document.getElementById(this.activeDescendant);
      listitem.scrollIntoView({ block: 'nearest', inline: 'nearest' });
    }
  }

  focusFirstItem() {
    var firstItem = this.listboxNode.querySelector('[role="option"]');

    if (firstItem) {
      this.focusItem(firstItem);
    }
  }

  focusLastItem() {
    const itemList = this.listboxNode.querySelectorAll('[role="option"]');

    if (itemList.length) {
      this.focusItem(itemList[itemList.length - 1]);
    }
  }

  checkKeyPress(evt) {
    const lastActiveId = this.activeDescendant;
    const allOptions = this.listboxNode.querySelectorAll('[role="option"]');
    const currentItem =
      document.getElementById(this.activeDescendant) || allOptions[0];
    let nextItem = currentItem;

    if (!currentItem) {
      return;
    }

    switch (evt.key) {
      case 'PageUp':
      case 'PageDown':
        evt.preventDefault();
        if (this.moveUpDownEnabled) {
          if (evt.key === 'PageUp') {
            this.moveUpItems();
          } else {
            this.moveDownItems();
          }
        }

        break;
      case 'ArrowUp':
      case 'ArrowDown':
        evt.preventDefault();
        if (!this.activeDescendant) {
          // focus first option if no option was previously focused, and perform no other actions
          this.focusItem(currentItem);
          break;
        }

        if (this.moveUpDownEnabled && evt.altKey) {
          evt.preventDefault();
          if (evt.key === 'ArrowUp') {
            this.moveUpItems();
          } else {
            this.moveDownItems();
          }
          this.updateScroll();
          return;
        }

        if (evt.key === 'ArrowUp') {
          nextItem = this.findPreviousOption(currentItem);
        } else {
          nextItem = this.findNextOption(currentItem);
        }

        if (nextItem && this.multiselectable && event.shiftKey) {
          this.selectRange(this.startRangeIndex, nextItem);
        }

        if (nextItem) {
          this.focusItem(nextItem);
        }

        break;

      case 'Home':
        evt.preventDefault();
        this.focusFirstItem();

        if (this.multiselectable && evt.shiftKey && evt.ctrlKey) {
          this.selectRange(this.startRangeIndex, 0);
        }
        break;

      case 'End':
        evt.preventDefault();
        this.focusLastItem();

        if (this.multiselectable && evt.shiftKey && evt.ctrlKey) {
          this.selectRange(this.startRangeIndex, allOptions.length - 1);
        }
        break;

      case 'Shift':
        this.startRangeIndex = this.getElementIndex(currentItem, allOptions);
        break;

      case ' ':
        evt.preventDefault();
        this.toggleSelectItem(nextItem);
        break;

      case 'Backspace':
      case 'Delete':
      case 'Enter':
        if (!this.moveButton) {
          return;
        }

        var keyshortcuts = this.moveButton.getAttribute('aria-keyshortcuts');
        if (evt.key === 'Enter' && keyshortcuts.indexOf('Enter') === -1) {
          return;
        }
        if (
          (evt.key === 'Backspace' || evt.key === 'Delete') &&
          keyshortcuts.indexOf('Delete') === -1
        ) {
          return;
        }

        evt.preventDefault();

        var nextUnselected = nextItem.nextElementSibling;
        while (nextUnselected) {
          if (nextUnselected.getAttribute('aria-selected') != 'true') {
            break;
          }
          nextUnselected = nextUnselected.nextElementSibling;
        }
        if (!nextUnselected) {
          nextUnselected = nextItem.previousElementSibling;
          while (nextUnselected) {
            if (nextUnselected.getAttribute('aria-selected') != 'true') {
              break;
            }
            nextUnselected = nextUnselected.previousElementSibling;
          }
        }

        this.moveItems();

        if (!this.activeDescendant && nextUnselected) {
          this.focusItem(nextUnselected);
        }
        break;

      case 'A':
      case 'a':
        // handle control + A
        if (evt.ctrlKey || evt.metaKey) {
          if (this.multiselectable) {
            this.selectRange(0, allOptions.length - 1);
          }
          evt.preventDefault();
          break;
        }
      // fall through
      default:
        if (evt.key.length === 1) {
          const itemToFocus = this.findItemToFocus(evt.key.toLowerCase());
          if (itemToFocus) {
            this.focusItem(itemToFocus);
          }
        }
        break;
    }

    if (this.activeDescendant !== lastActiveId) {
      this.updateScroll();
    }
  }

  findItemToFocus(character) {
    const itemList = this.listboxNode.querySelectorAll('[role="option"]');
    let searchIndex = 0;

    if (!this.keysSoFar) {
      for (let i = 0; i < itemList.length; i++) {
        if (itemList[i].getAttribute('id') == this.activeDescendant) {
          searchIndex = i;
        }
      }
    }

    this.keysSoFar += character;
    this.clearKeysSoFarAfterDelay();

    let nextMatch = this.findMatchInRange(
      itemList,
      searchIndex + 1,
      itemList.length
    );

    if (!nextMatch) {
      nextMatch = this.findMatchInRange(itemList, 0, searchIndex);
    }
    return nextMatch;
  }

  /* Return the index of the passed element within the passed array, or null if not found */
  getElementIndex(option, options) {
    const allOptions = Array.prototype.slice.call(options); // convert to array
    const optionIndex = allOptions.indexOf(option);

    return typeof optionIndex === 'number' ? optionIndex : null;
  }

  /* Return the next listbox option, if it exists; otherwise, returns null */
  findNextOption(currentOption) {
    const allOptions = Array.prototype.slice.call(
      this.listboxNode.querySelectorAll('[role="option"]')
    ); // get options array
    const currentOptionIndex = allOptions.indexOf(currentOption);
    let nextOption = null;

    if (currentOptionIndex > -1 && currentOptionIndex < allOptions.length - 1) {
      nextOption = allOptions[currentOptionIndex + 1];
    }

    return nextOption;
  }

  /* Return the previous listbox option, if it exists; otherwise, returns null */
  findPreviousOption(currentOption) {
    const allOptions = Array.prototype.slice.call(
      this.listboxNode.querySelectorAll('[role="option"]')
    ); // get options array
    const currentOptionIndex = allOptions.indexOf(currentOption);
    let previousOption = null;

    if (currentOptionIndex > -1 && currentOptionIndex > 0) {
      previousOption = allOptions[currentOptionIndex - 1];
    }

    return previousOption;
  }

  clearKeysSoFarAfterDelay() {
    if (this.keyClear) {
      clearTimeout(this.keyClear);
      this.keyClear = null;
    }
    this.keyClear = setTimeout(
      function () {
        this.keysSoFar = '';
        this.keyClear = null;
      }.bind(this),
      500
    );
  }

  findMatchInRange(list, startIndex, endIndex) {
    // Find the first item starting with the keysSoFar substring, searching in
    // the specified range of items
    for (let n = startIndex; n < endIndex; n++) {
      const label = list[n].innerText;
      if (label && label.toLowerCase().indexOf(this.keysSoFar) === 0) {
        return list[n];
      }
    }
    return null;
  }

  checkClickItem(evt) {
    if (evt.target.getAttribute('role') !== 'option') {
      return;
    }

    this.focusItem(evt.target);
    this.toggleSelectItem(evt.target);
    this.updateScroll();

    if (this.multiselectable && evt.shiftKey) {
      this.selectRange(this.startRangeIndex, evt.target);
    }
  }

  /**
   * Prevent text selection on shift + click for multi-select listboxes
   *
   * @param evt
   */
  checkMouseDown(evt) {
    if (
      this.multiselectable &&
      evt.shiftKey &&
      evt.target.getAttribute('role') === 'option'
    ) {
      evt.preventDefault();
    }
  }

  /**
   * @description
   *  Toggle the aria-selected value
   * @param element
   *  The element to select
   */
  toggleSelectItem(element) {
    if (this.multiselectable) {
      element.setAttribute(
        'aria-selected',
        element.getAttribute('aria-selected') === 'true' ? 'false' : 'true'
      );

      this.updateMoveButton();
    }
  }

  /**
   * @description
   *  Defocus the specified item
   * @param element
   *  The element to defocus
   */
  defocusItem(element) {
    if (!element) {
      return;
    }
    if (!this.multiselectable) {
      element.removeAttribute('aria-selected');
    }
    element.classList.remove('focused');
  }

  /**
   * @description
   *  Focus on the specified item
   * @param element
   *  The element to focus
   */
  focusItem(element) {
    this.defocusItem(document.getElementById(this.activeDescendant));
    if (!this.multiselectable) {
      element.setAttribute('aria-selected', 'true');
    }
    element.classList.add('focused');
    this.listboxNode.setAttribute('aria-activedescendant', element.id);
    this.activeDescendant = element.id;

    if (!this.multiselectable) {
      this.updateMoveButton();
    }

    this.checkUpDownButtons();
    this.handleFocusChange(element);
  }

  /**
   * Helper function to check if a number is within a range; no side effects.
   *
   * @param index
   * @param start
   * @param end
   * @returns {boolean}
   */
  checkInRange(index, start, end) {
    const rangeStart = start < end ? start : end;
    const rangeEnd = start < end ? end : start;

    return index >= rangeStart && index <= rangeEnd;
  }

  /**
   * Select a range of options
   *
   * @param start
   * @param end
   */
  selectRange(start, end) {
    // get start/end indices
    const allOptions = this.listboxNode.querySelectorAll('[role="option"]');
    const startIndex =
      typeof start === 'number'
        ? start
        : this.getElementIndex(start, allOptions);
    const endIndex =
      typeof end === 'number' ? end : this.getElementIndex(end, allOptions);

    for (let index = 0; index < allOptions.length; index++) {
      const selected = this.checkInRange(index, startIndex, endIndex);
      allOptions[index].setAttribute('aria-selected', selected + '');
    }

    this.updateMoveButton();
  }

  /**
   * Check for selected options and update moveButton, if applicable
   */
  updateMoveButton() {
    if (!this.moveButton) {
      return;
    }

    if (this.listboxNode.querySelector('[aria-selected="true"]')) {
      this.moveButton.setAttribute('aria-disabled', 'false');
    } else {
      this.moveButton.setAttribute('aria-disabled', 'true');
    }
  }

  /**
   * Check if the selected option is in view, and scroll if not
   */
  updateScroll() {
    const selectedOption = document.getElementById(this.activeDescendant);
    if (selectedOption) {
      const scrollBottom =
        this.listboxNode.clientHeight + this.listboxNode.scrollTop;
      const elementBottom =
        selectedOption.offsetTop + selectedOption.offsetHeight;
      if (elementBottom > scrollBottom) {
        this.listboxNode.scrollTop =
          elementBottom - this.listboxNode.clientHeight;
      } else if (selectedOption.offsetTop < this.listboxNode.scrollTop) {
        this.listboxNode.scrollTop = selectedOption.offsetTop;
      }
      selectedOption.scrollIntoView({ block: 'nearest', inline: 'nearest' });
    }
  }

  /**
   * @description
   *  Enable/disable the up/down arrows based on the activeDescendant.
   */
  checkUpDownButtons() {
    const activeElement = document.getElementById(this.activeDescendant);

    if (!this.moveUpDownEnabled) {
      return;
    }

    if (!activeElement) {
      this.upButton.setAttribute('aria-disabled', 'true');
      this.downButton.setAttribute('aria-disabled', 'true');
      return;
    }

    if (this.upButton) {
      if (activeElement.previousElementSibling) {
        this.upButton.setAttribute('aria-disabled', false);
      } else {
        this.upButton.setAttribute('aria-disabled', 'true');
      }
    }

    if (this.downButton) {
      if (activeElement.nextElementSibling) {
        this.downButton.setAttribute('aria-disabled', false);
      } else {
        this.downButton.setAttribute('aria-disabled', 'true');
      }
    }
  }

  /**
   * @description
   *  Add the specified items to the listbox. Assumes items are valid options.
   * @param items
   *  An array of items to add to the listbox
   */
  addItems(items) {
    if (!items || !items.length) {
      return;
    }

    items.forEach(
      function (item) {
        this.defocusItem(item);
        this.toggleSelectItem(item);
        this.listboxNode.append(item);
      }.bind(this)
    );

    if (!this.activeDescendant) {
      this.focusItem(items[0]);
    }

    this.handleItemChange('added', items);
  }

  /**
   * @description
   *  Remove all of the selected items from the listbox; Removes the focused items
   *  in a single select listbox and the items with aria-selected in a multi
   *  select listbox.
   * @returns {Array}
   *  An array of items that were removed from the listbox
   */
  deleteItems() {
    let itemsToDelete;

    if (this.multiselectable) {
      itemsToDelete = this.listboxNode.querySelectorAll(
        '[aria-selected="true"]'
      );
    } else if (this.activeDescendant) {
      itemsToDelete = [document.getElementById(this.activeDescendant)];
    }

    if (!itemsToDelete || !itemsToDelete.length) {
      return [];
    }

    itemsToDelete.forEach(
      function (item) {
        item.remove();

        if (item.id === this.activeDescendant) {
          this.clearActiveDescendant();
        }
      }.bind(this)
    );

    this.handleItemChange('removed', itemsToDelete);

    return itemsToDelete;
  }

  clearActiveDescendant() {
    this.activeDescendant = null;
    this.listboxNode.setAttribute('aria-activedescendant', null);

    this.updateMoveButton();
    this.checkUpDownButtons();
  }

  /**
   * @description
   *  Shifts the currently focused item up on the list. No shifting occurs if the
   *  item is already at the top of the list.
   */
  moveUpItems() {
    if (!this.activeDescendant) {
      return;
    }

    const currentItem = document.getElementById(this.activeDescendant);
    const previousItem = currentItem.previousElementSibling;

    if (previousItem) {
      this.listboxNode.insertBefore(currentItem, previousItem);
      this.handleItemChange('moved_up', [currentItem]);
    }

    this.checkUpDownButtons();
  }

  /**
   * @description
   *  Shifts the currently focused item down on the list. No shifting occurs if
   *  the item is already at the end of the list.
   */
  moveDownItems() {
    if (!this.activeDescendant) {
      return;
    }

    var currentItem = document.getElementById(this.activeDescendant);
    var nextItem = currentItem.nextElementSibling;

    if (nextItem) {
      this.listboxNode.insertBefore(nextItem, currentItem);
      this.handleItemChange('moved_down', [currentItem]);
    }

    this.checkUpDownButtons();
  }

  /**
   * @description
   *  Delete the currently selected items and add them to the sibling list.
   */
  moveItems() {
    if (!this.siblingList) {
      return;
    }

    var itemsToMove = this.deleteItems();
    this.siblingList.addItems(itemsToMove);
  }

  /**
   * @description
   *  Enable Up/Down controls to shift items up and down.
   * @param upButton
   *   Up button to trigger up shift
   * @param downButton
   *   Down button to trigger down shift
   */
  enableMoveUpDown(upButton, downButton) {
    this.moveUpDownEnabled = true;
    this.upButton = upButton;
    this.downButton = downButton;
    upButton.addEventListener('click', this.moveUpItems.bind(this));
    downButton.addEventListener('click', this.moveDownItems.bind(this));
  }

  /**
   * @description
   *  Enable Move controls. Moving removes selected items from the current
   *  list and adds them to the sibling list.
   * @param button
   *   Move button to trigger delete
   * @param siblingList
   *   Listbox to move items to
   */
  setupMove(button, siblingList) {
    this.siblingList = siblingList;
    this.moveButton = button;
    button.addEventListener('click', this.moveItems.bind(this));
  }

  setHandleItemChange(handlerFn) {
    this.handleItemChange = handlerFn;
  }

  setHandleFocusChange(focusChangeHandler) {
    this.handleFocusChange = focusChangeHandler;
  }
};
/*
 *   This content is licensed according to the W3C Software License at
 *   https://www.w3.org/Consortium/Legal/2015/copyright-software-and-document
 */

'use strict';

/**
 * @namespace aria
 * @description
 * The aria namespace is used to support sharing class definitions between example files
 * without causing eslint errors for undefined classes
 */
var aria = aria || {};

/**
 * @class
 * @description
 *  Toolbar object representing the state and interactions for a toolbar widget
 * @param toolbarNode
 *  The DOM node pointing to the toolbar
 */

aria.Toolbar = class Toolbar {
  constructor(toolbarNode) {
    this.toolbarNode = toolbarNode;
    this.items = this.toolbarNode.querySelectorAll('.toolbar-item');
    this.selectedItem = this.toolbarNode.querySelector('.selected');
    this.registerEvents();
  }

  /**
   * @description
   *  Register events for the toolbar interactions
   */
  registerEvents() {
    this.toolbarNode.addEventListener(
      'keydown',
      this.checkFocusChange.bind(this)
    );
    this.toolbarNode.addEventListener('click', this.checkClickItem.bind(this));
  }

  /**
   * @description
   *  Handle various keyboard commands to move focus:
   *    LEFT:  Previous button
   *    RIGHT: Next button
   *    HOME:  First button
   *    END:   Last button
   * @param evt
   *  The keydown event object
   */
  checkFocusChange(evt) {
    let nextIndex, nextItem;

    // Do not move focus if any modifier keys pressed
    if (!evt.shiftKey && !evt.metaKey && !evt.altKey && !evt.ctrlKey) {
      switch (evt.key) {
        case 'ArrowLeft':
        case 'ArrowRight':
          nextIndex = Array.prototype.indexOf.call(
            this.items,
            this.selectedItem
          );
          nextIndex = evt.key === 'ArrowLeft' ? nextIndex - 1 : nextIndex + 1;
          nextIndex = Math.max(Math.min(nextIndex, this.items.length - 1), 0);

          nextItem = this.items[nextIndex];
          break;

        case 'End':
          nextItem = this.items[this.items.length - 1];
          break;

        case 'Home':
          nextItem = this.items[0];
          break;
      }

      if (nextItem) {
        this.selectItem(nextItem);
        this.focusItem(nextItem);
        evt.stopPropagation();
        evt.preventDefault();
      }
    }
  }

  /**
   * @description
   *  Selects a toolbar item if it is clicked
   * @param evt
   *  The click event object
   */
  checkClickItem(evt) {
    if (evt.target.classList.contains('toolbar-item')) {
      this.selectItem(evt.target);
    }
  }

  /**
   * @description
   *  Deselect the specified item
   * @param element
   *  The item to deselect
   */
  deselectItem(element) {
    element.classList.remove('selected');
    element.setAttribute('aria-selected', 'false');
    element.setAttribute('tabindex', '-1');
  }

  /**
   * @description
   *  Deselect the currently selected item and select the specified item
   * @param element
   *  The item to select
   */
  selectItem(element) {
    this.deselectItem(this.selectedItem);
    element.classList.add('selected');
    element.setAttribute('aria-selected', 'true');
    element.setAttribute('tabindex', '0');
    this.selectedItem = element;
  }

  /**
   * @description
   *  Focus on the specified item
   * @param element
   *  The item to focus on
   */
  focusItem(element) {
    element.focus();
  }
};
/*
 *   This content is licensed according to the W3C Software License at
 *   https://www.w3.org/Consortium/Legal/2015/copyright-software-and-document
 */

'use strict';

/**
 * @namespace aria
 * @description
 * The aria namespace is used to support sharing class definitions between example files
 * without causing eslint errors for undefined classes
 */
var aria = aria || {};

/**
 * ARIA Listbox Examples
 *
 * @function onload
 * @description Initialize the listbox examples once the page has loaded
 */

window.addEventListener('load', function () {
  // This onload handle initializes two examples. Only initialize example if the example
  // can be found in the dom.
  if (document.getElementById('ss_imp_list')) {
    var ex1ImportantListbox = new aria.Listbox(
      document.getElementById('ss_imp_list')
    );
    var ex1UnimportantListbox = new aria.Listbox(
      document.getElementById('ss_unimp_list')
    );
    new aria.Toolbar(document.querySelector('[role="toolbar"]'));

    ex1ImportantListbox.enableMoveUpDown(
      document.getElementById('ex1-up'),
      document.getElementById('ex1-down')
    );
    ex1ImportantListbox.setupMove(
      document.getElementById('ex1-delete'),
      ex1UnimportantListbox
    );
    ex1ImportantListbox.setHandleItemChange(function (event, items) {
      var updateText = '';

      switch (event) {
        case 'added':
          updateText =
            'Moved ' + items[0].innerText + ' to important features.';
          break;
        case 'removed':
          updateText =
            'Moved ' + items[0].innerText + ' to unimportant features.';
          break;
        case 'moved_up':
        case 'moved_down':
          var pos = Array.prototype.indexOf.call(
            this.listboxNode.children,
            items[0]
          );
          pos++;
          updateText = 'Moved to position ' + pos;
          break;
      }

      if (updateText) {
        var ex1LiveRegion = document.getElementById('ss_live_region');
        ex1LiveRegion.innerText = updateText;
      }
    });
    ex1UnimportantListbox.setupMove(
      document.getElementById('ex1-add'),
      ex1ImportantListbox
    );
  }

  // This onload handle initializes two examples. Only initialize example if the example
  // can be found in the dom.
  if (document.getElementById('ms_imp_list')) {
    var ex2ImportantListbox = new aria.Listbox(
      document.getElementById('ms_imp_list')
    );
    var ex2UnimportantListbox = new aria.Listbox(
      document.getElementById('ms_unimp_list')
    );

    ex2ImportantListbox.setupMove(
      document.getElementById('ex2-add'),
      ex2UnimportantListbox
    );
    ex2UnimportantListbox.setupMove(
      document.getElementById('ex2-delete'),
      ex2ImportantListbox
    );
    ex2UnimportantListbox.setHandleItemChange(function (event, items) {
      var updateText = '';
      var itemText = items.length === 1 ? '1 item' : items.length + ' items';

      switch (event) {
        case 'added':
          updateText = 'Added ' + itemText + ' to chosen features.';
          break;
        case 'removed':
          updateText = 'Removed ' + itemText + ' from chosen features.';
          break;
      }

      if (updateText) {
        var ex1LiveRegion = document.getElementById('ms_live_region');
        ex1LiveRegion.innerText = updateText;
      }
    });
  }
});

</script>  
 
  
@endsection
