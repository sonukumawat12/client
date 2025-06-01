<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Terms Condition</title>
    {{-- @include('layouts.partials.css') --}}
    <style>
        

.page {
  padding: 50px 80px;
  background: white;
  /* box-shadow: 2px 2px 2px rgba(0,0,0,0.6); */
 
}
div#container {
            font: normal 12px Arial, Helvetica, Sans-serif;
            background: white;
            display: inline-flex;
            width: 100%; 
        }

#terms-and-conditions {
  font-size: 14px; // default

  h1 {
    font-size: 34px;
  }
  
  ol {
    counter-reset: item;
  }

  li {
    display: block;
    margin: 20px 0;
    position: relative;
  }
  
  li:before {
    position: absolute;
    top: 0;
    margin-left: -50px;
    color: magenta;
    content: counters(item, ".") "    ";
    counter-increment: item;
  }
  
}
button{
    width: 66px;
    height: 40px;
    float: left;
    margin-left: 47%;
    margin-bottom: 10px;
    background-color: #7d3aed;
    color: #ffff;
    border: none;
    border-radius: 10px;
}

    </style>
</head>

<body>
        <div class="page" id="container">
  <div id="terms-and-conditions" >
    <h1>Terms & Conditions Template</h1>
    <ol>
      <li>
        <b>INTELLECTUAL PROPERTY, LICENCE AND RESTRICTIONS</b>
        <ol>
          <li>
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut vitae elementum ipsum. Nam eget congue libero. Donec at elit eget ante pulvinar dictum ac in lorem. Sed sed molestie mi, sit amet convallis erat. Aliquam eu sagittis nulla. Nulla id mollis dolor. Pellentesque sagittis odio a blandit ultricies.
          </li>
          <li>Subject to your compliance with these Terms, the Developer grants you a limited, non-exclusive, revocable, non-transferrable licence to:
            <ol>
              <li>lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut vitae elementum ipsum. Nam eget congue libero. Donec at elit eget ante pulvinar dictum ac in lorem. Sed sed molestie mi, sit amet convallis erat. Aliquam eu sagittis nulla. Nulla id mollis dolor. Pellentesque sagittis odio a blandit ultricies.</li>
              <li>lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut vitae elementum ipsum. Nam eget congue libero. Donec at elit eget ante pulvinar dictum ac in lorem. Sed sed molestie mi, sit amet convallis erat. Aliquam eu sagittis nulla. Nulla id mollis dolor. Pellentesque sagittis odio a blandit ultricies.</li>
            </ol>
          </li>
          <li>Any rights not expressly granted herein are reserved by the Developer.</li>
          <li>You may not:</li>
          <ol>
            <li>etiam quis purus eget tortor efficitur tempor. Sed volutpat, dolor in porta aliquam, quam enim accumsan dui.</li>
            <li>etiam quis purus eget tortor efficitur tempor. Sed volutpat, dolor in porta aliquam, quam enim accumsan dui.</li>
            <li>etiam quis purus eget tortor efficitur tempor. Sed volutpat, dolor in porta aliquam, quam enim accumsan dui.</li>
            <li>etiam quis purus eget tortor efficitur tempor. Sed volutpat, dolor in porta aliquam, quam enim accumsan dui.</li>
            <li>etiam quis purus eget tortor efficitur tempor. Sed volutpat, dolor in porta aliquam, quam enim accumsan dui.</li>
            <li>praesent sagittis pharetra justo vehicula tincidunt. Cras ut augue non massa gravida porttitor at et dolor. Ut dolor urna, fringilla eu auctor vel, tristique et turpis. Vestibulum nec massa ac nisi dignissim egestas. Vestibulum non malesuada urna. In hac habitasse platea dictumst. Vestibulum vel leo mattis, efficitur lorem et, rutrum velit. Cras venenatis semper diam, non tempor eros luctus ut. Aenean id diam orci. Praesent a nisl vehicula, aliquam odio id, mattis velit.</li>
          </ol>
        </ol>
      </li>

        <ol>
      

  </div><!--  end #terms-and-conditions  -->
</div><!--  end .page  -->

    <button type="button" id="print" onclick="print_page(this)">Print</button>
    
<script>
function print_page(obj){
    var x = document.getElementById("print");
    x.style.display = "none";
    window.print();
}    
</script>
</body>

</html>
