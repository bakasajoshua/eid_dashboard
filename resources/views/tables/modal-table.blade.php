
<div class="modal fade" tabindex="-1" role="dialog" id="{{ $modal_div }}">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"> {{ $modal_title ?? 'POC Site Details' }} </h4>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table id="{{ $div }}"  class="table table-striped table-bordered" style="background:#CCC;">
            <thead>            
                {!! $th !!}
            </thead>
            <tbody>
              {!!$outcomes!!}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript" charset="utf-8">
  $(document).ready(function() {
  	
  	// $('#example').DataTable();

  	$("#{{ $div }}").DataTable({
      dom: '<"btn "B>lTfgtip',
      responsive: false,
        buttons : [
            {
              text:  'Export to CSV',
              extend: 'csvHtml5',
              title: 'Download'
            },
            {
              text:  'Export to Excel',
              extend: 'excelHtml5',
              title: 'Download'
            }
          ]
  	});
    $("#{{ $modal_div }}").modal('show');
   
  });
</script>