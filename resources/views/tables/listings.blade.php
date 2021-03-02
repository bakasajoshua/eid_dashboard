<!-- <div class="list-group" style="height: 362px;"> -->
<div class="list-group">
	@foreach($rows as $key => $row)
		@break($key == 15)
		<?php 
			$row = get_object_vars($row);  
			if(($row['pos'] + $row['neg'])){
				$positivity = round(($row['pos'] / ($row['pos'] + $row['neg']) * 100), 1);
			}else{
				$positivity = 0;
			}
		?>
			<a href="javascript:void(0);" class="list-group-item" ><strong>{{ ($key+1) }}.</strong> {{ $row['name'] }} {{ $positivity }}% ({{ number_format($row['pos']) }}) </a>
	@endforeach
</div>

<button class="btn btn-primary"  onclick="expand{{ $level }}Listing();" style="background-color: #1BA39C;color: white; margin-top: 1em;margin-bottom: 1em;">
	View Full Listing
</button>


<div class="modal fade" tabindex="-1" role="dialog" id="exp{{ $level }}List">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">{{ $level }} Listing</h4>
			</div>
			<div class="modal-body">
				<table id="{{ $div }}" cellspacing="1" cellpadding="3" class="tablehead table table-striped table-bordered" style="max-width: 100%;">
					<thead>
						<tr>
							<th>#</th>
							<th>Name</th>
							<th>% Positivity</th>
							<th>Positives</th>
							<th>Negatives</th>
						</tr>
					</thead>
					<tbody>
						@foreach($rows as $key => $row)
							<?php $row = get_object_vars($row);
								if(($row['pos'] + $row['neg'])){
									$positivity = round(($row['pos'] / ($row['pos'] + $row['neg']) * 100), 1);
								}else{
									$positivity = 0;
								} 
							?>
							<tr>
								<td> {{ ($key+1) }} </td>
								<td> {{ $row['name'] }} </td>
								<td> {{ $positivity }} </td>
								<td> {{ number_format($row['pos']) }} </td>
								<td> {{ number_format($row['neg']) }} </td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		$('#{{ $div }}').DataTable({
			dom: '<"btn btn-primary"B>lTfgtip',
			responsive: true,
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
	});

	function <?php echo 'expand' . $level . 'Listing';  ?> ()
	{
		$('#exp{{ $level }}List').modal('show');
	}
</script>