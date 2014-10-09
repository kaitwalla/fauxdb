<? $v = (!empty($_GET)) ? $_GET : $_POST; ?>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
 <script type="text/javascript" src="//cdn.datatables.net/1.10.2/js/jquery.dataTables.min.js"></script>
 <script src="http://projects.ydr.com/fauxdb/js/jquery.modal.min.js" type="text/javascript" charset="utf-8"></script>
 <link rel="stylesheet" href="//cdn.datatables.net/1.10.2/css/jquery.dataTables.min.css" />
 <link rel="stylesheet" href="<?=$v['projects_url']?>/css/jquery.modal.css" type="text/css" media="screen" />
   <div class="start" style="text-align:center; font-family: sans-serif; background:white; display:none;">
   <input type="text" class="dbSearch" value="<?=$v['search_text']?>" />
   <a style="background:#2c3e50;" class="search">Search</a><a style="background:#BBB;">View all</a>
  </div>
  <div class="loading" style="text-align:center; width:640px; display:none;">
   <img src="<?=$v['projects_url']?>/img/loading.gif" class="hiddenPic" />
   <p style="font-size:20px; width:50%; font-family:sans-serif; text-align:center; margin:10px auto;">Please wait, loading ...</p>
  </div>
 <div class="table" style="display:none;">
  <p class="clicksplain"><em>Select any row to see full details</em></p>
  <table id="dtable_target">
  <thead>
   <tr>
   </tr>
  </thead>
  <tbody>
  </tbody>
  </table>
 </div>
 <div class="modal">
  <table>
  </table>
 </div>
 <script type="text/javascript">
   var cols;
   var dTable;
   var row;
   var first = false; 
   var search = false;
   var shown = false;
   var test;

   function dtable_init() {
    $.ajax({
     url:'<?=$v['projects_url']?>/data/<?=$v['json_name']?>.js',
     dataType: 'jsonp',
     callback: 'jsonpCallback'
    });
 }

 function jsonpCallback(resp) {
  e = resp;
  for (var i = 0; i < e.columns.length; i++) {
     $('#dtable_target thead tr').append('<th>'+e.columns[i]+'</th>');
    }
    cols = e.columns;
     dTable = $('#dtable_target').DataTable({ 
      data:e.data, 
      columnDefs: [
       {
        targets: [<?=implode(',',unserialize(stripcslashes($v['fields_hidden']))); ?>],
        visible: false
       }
      ]
     });
     (search) ? dTable.search(search).draw() : false;
 }

    $(function() {
     $('#dtable_target').on('init.dt',function() {
      if (!shown) {
       $('.loading').hide();
       $('.table').fadeIn();
       shown = !shown;
      }
     });
     $('.start').show().on('click','a',function() {
   $('.start').hide();
   $('.loading').show();
      if ($(this).hasClass('search')) {
       search = ($('.dbSearch').val() == "Search database") ? false : $('.dbSearch').val();
      }
      dtable_init();
     });
     $('.start input').on('focus',function() {
      if (!first) {
       $(this).val('');
       first = !first;
      }
     }).on('keyup',function(e) {
      var code = e.keyCode || e.which;
      if (code == 13) {
       $('.start a.search').click();
      }
     });
     $('#dtable_target tbody').on('click','tr',function() {
      $(this).addClass('selected');
      //var row
      row = dTable.row('.selected').data();
      $('.modal table').html('');
      for (var i = 0; i < cols.length; i++) {
       $('<tr><td><strong>'+cols[i]+'</strong></td><td>'+row[i]+'</td></tr>').appendTo('.modal table');
      }
      $('.modal').modal().on($.modal.BEFORE_CLOSE,function() {
       $('tr.selected').removeClass('selected');
      });
     });
    });
 </script>
<p><a href="<?=$v['projects_url']?>/index.php?db=<?=$v['id']?>" target="_blank" style="margin-bottom:20px; margin-top:20px; display:block;">View data in fullscreen</a></p>
