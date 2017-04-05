<style>
  .col-sm-12 {width:100%;float:left;}
  .col-sm-5 {width:41.5%;float:left;}
  .col-sm-7 {width:57.5%;float:left;}
  .col-sm-6 {width:40%;margin:0 5%;float:left;}
  .col-sm-6 select, .col-sm-6 input {width:inherit;display:inherit;}
  input.form-control.input-sm, select.form-control.input-sm {margin-left:10px;font-size:14px;height:30px;margin-bottom:0px;}
  div#dataTables-example_info, ul.pagination {margin:0;text-align: center;}
  #dataTables-example_wrapper div.row {margin-left:0px;margin-right:0px;}
  .odd {background-color:#F9F9F9;}
  .even {background-color:#F3F3F3;}
</style>
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>

<script src="https://cdn.datatables.net/r/bs-3.3.5/jqc-1.11.3,dt-1.10.8/datatables.min.js"></script>
<script>
    $('#dataTables-example').dataTable();
    $('#dataTables-example')
      .removeClass( 'display' )
      .addClass('table table-striped table-bordered');
</script>