@extends('user.layouts.app')
@section('title', config('setting.site_name').' - Transaction List')
@section('header_title', 'Transaction List')
@section('content')
<link rel="stylesheet" href="{{ asset('assets/datatable/jquery.dataTables.min.css') }}">


<div class="container-fluid p-0">
	<!-- Filter Row -->
	<div class="row g-2">
		<div class=" col-md-4 col-lg-2">
			<input type="text" class="filter-input form-control default-input content-3" id="filter-name" placeholder="Filter by Name">
		</div>
		<div class=" col-md-4 col-lg-2">
			<input type="text" class="filter-input form-control default-input content-3" id="filter-position" placeholder="Filter by Position">
		</div>
		<div class=" col-md-4 col-lg-2">
			<input type="text" class="filter-input form-control default-input content-3" id="filter-office" placeholder="Filter by Office">
		</div>
		<div class=" col-md-4 col-lg-2">
			<input type="text" class="filter-input form-control default-input content-3" id="filter-age" placeholder="Filter by Age">
		</div>
		<div class=" col-md-4 col-lg-2">
			<input type="text" class="filter-input form-control default-input content-3" id="filter-start-date" placeholder="Filter by Start Date">
		</div>
		<div class=" col-md-4 col-lg-2">
			<input type="text" class="filter-input form-control default-input content-3" id="filter-salary" placeholder="Filter by Salary">
		</div>
		<div class="filter-buttons col-12 text-end">
			<button id="apply-filters" class="btn btn-primary content-3">Filter</button>
			<button id="reset-filters" class="btn btn-secondary content-3">Reset</button>
		</div>
	</div>
	<hr>
	<div class="data-table-container">
		<table id="data-table" class="table table-borderless table-hover border-0 mb-4">
		<thead>
			<tr>
			<th>Name</th>
			<th>Position</th>
			<th>Office</th>
			<th>Age</th>
			<th>Start date</th>
			<th>Salary.</th>
			</tr>
		</thead>
	
		<tbody>
			<tr>
			<td>Tiger Nixon</td>
			<td>System Architect</td>
			<td>Edinburgh</td>
			<td>61</td>
			<td>2011/04/25</td>
			<td>$320,800</td>
			</tr>
			<tr>
			<td>Garrett Winters</td>
			<td>Accountant</td>
			<td><span class="badge text-bg-success fw-normal opacity-75">Active</span></td>
			<td>63</td>
			<td>2011/07/25</td>
			<td>$170,750</td>
			</tr>
			<tr>
			<td>Ashton Cox</td>
			<td>Junior Technical Author</td>
			<td><span class="badge text-bg-danger fw-normal opacity-75">Active</span></td>
			<td>66</td>
			<td>2009/01/12</td>
			<td>$86,000</td>
			</tr>
			<tr>
			<td>Cedric Kelly</td>
			<td>Senior Javascript Developer</td>
			<td><span class="badge text-bg-warning fw-normal opacity-75">Active</span></td>
			<td>22</td>
			<td>2012/03/29</td>
			<td>$433,060</td>
			</tr>
			<tr>
			<td>Airi Satou</td>
			<td>Accountant</td>
			<td><span class="badge text-bg-secondary fw-normal opacity-75">Active</span></td>
			<td>33</td>
			<td>2008/11/28</td>
			<td>$162,700</td>
			</tr>
			<tr>
			<td>Brielle Williamson</td>
			<td>Integration Specialist</td>
			<td>New York</td>
			<td>61</td>
			<td>2012/12/02</td>
			<td>$372,000</td>
			</tr>
			<tr>
			<td>Herrod Chandler</td>
			<td>Sales Assistant</td>
			<td>San Francisco</td>
			<td>59</td>
			<td>2012/08/06</td>
			<td>$137,500</td>
			</tr>
			<tr>
			<td>Garrett Winters</td>
			<td>Accountant</td>
			<td>Tokyo</td>
			<td>63</td>
			<td>2011/07/25</td>
			<td>$170,750</td>
			</tr>
			<tr>
			<td>Herrod Chandler</td>
			<td>Sales Assistant</td>
			<td>San Francisco</td>
			<td>59</td>
			<td>2012/08/06</td>
			<td>$137,500</td>
			</tr>
			<tr>
			<td>Brielle Williamson</td>
			<td>Integration Specialist</td>
			<td>New York</td>
			<td>61</td>
			<td>2012/12/02</td>
			<td>$372,000</td>
			</tr>
			<tr>
			<td>Herrod Chandler</td>
			<td>Sales Assistant</td>
			<td>San Francisco</td>
			<td>59</td>
			<td>2012/08/06</td>
			<td>$137,500</td>
			</tr>
			<tr>
			<td>Garrett Winters</td>
			<td>Accountant</td>
			<td>Tokyo</td>
			<td>63</td>
			<td>2011/07/25</td>
			<td>$170,750</td>
			</tr>
			<tr>
			<td>Herrod Chandler</td>
			<td>Sales Assistant</td>
			<td>San Francisco</td>
			<td>59</td>
			<td>2012/08/06</td>
			<td>$137,500</td>
			</tr>
			<tr>
			<td>Brielle Williamson</td>
			<td>Integration Specialist</td>
			<td>New York</td>
			<td>61</td>
			<td>2012/12/02</td>
			<td>$372,000</td>
			</tr>
			<tr>
			<td>Herrod Chandler</td>
			<td>Sales Assistant</td>
			<td>San Francisco</td>
			<td>59</td>
			<td>2012/08/06</td>
			<td>$137,500</td>
			</tr>
			<tr>
			<td>Garrett Winters</td>
			<td>Accountant</td>
			<td>Tokyo</td>
			<td>63</td>
			<td>2011/07/25</td>
			<td>$170,750</td>
			</tr>
			<tr>
			<td>Herrod Chandler</td>
			<td>Sales Assistant</td>
			<td>San Francisco</td>
			<td>59</td>
			<td>2012/08/06</td>
			<td>$137,500</td>
			</tr>
			<tr>
			<td>Brielle Williamson</td>
			<td>Integration Specialist</td>
			<td>New York</td>
			<td>61</td>
			<td>2012/12/02</td>
			<td>$372,000</td>
			</tr>
			<tr>
			<td>Herrod Chandler</td>
			<td>Sales Assistant</td>
			<td>San Francisco</td>
			<td>59</td>
			<td>2012/08/06</td>
			<td>$137,500</td>
			</tr>
			<tr>
			<td>Garrett Winters</td>
			<td>Accountant</td>
			<td>Tokyo</td>
			<td>63</td>
			<td>2011/07/25</td>
			<td>$170,750</td>
			</tr>
			<tr>
			<td>Herrod Chandler</td>
			<td>Sales Assistant</td>
			<td>San Francisco</td>
			<td>59</td>
			<td>2012/08/06</td>
			<td>$137,500</td>
			</tr>
			<tr>
			<td>Brielle Williamson</td>
			<td>Integration Specialist</td>
			<td>New York</td>
			<td>61</td>
			<td>2012/12/02</td>
			<td>$372,000</td>
			</tr>
			<tr>
			<td>Herrod Chandler</td>
			<td>Sales Assistant</td>
			<td>San Francisco</td>
			<td>59</td>
			<td>2012/08/06</td>
			<td>$137,500</td>
			</tr>
			<tr>
			<td>Garrett Winters</td>
			<td>Accountant</td>
			<td>Tokyo</td>
			<td>63</td>
			<td>2011/07/25</td>
			<td>$170,750</td>
			</tr>
			<tr>
			<td>Herrod Chandler</td>
			<td>Sales Assistant</td>
			<td>San Francisco</td>
			<td>59</td>
			<td>2012/08/06</td>
			<td>$137,500</td>
			</tr>
		</tbody>
		</table>
	</div>
</div>

@endsection

@push('js')
<script src="{{ asset('assets/datatable/jquery.dataTables.min.js')}}" ></script> 
<script>
	$(document).ready(function() {
            // Initialize DataTable
            var table = $('#data-table').DataTable();

            // Apply filters when the button is clicked
            $('#apply-filters').on('click', function() {
                // Get filter values
                var name = $('#filter-name').val();
                var position = $('#filter-position').val();
                var office = $('#filter-office').val();
                var age = $('#filter-age').val();
                var startDate = $('#filter-start-date').val();
                var salary = $('#filter-salary').val();

                // Apply filters to the respective columns
                table.column(0).search(name);
                table.column(1).search(position);
                table.column(2).search(office);
                table.column(3).search(age);
                table.column(4).search(startDate);
                table.column(5).search(salary);

                // Redraw the table with the new filters
                table.draw();
            });

			// Reset filters when the "Reset" button is clicked
            $('#reset-filters').on('click', function() {
                // Clear all filter inputs
                $('.filter-input').val('');

                // Clear filters for all columns
                table.columns().search('');

                // Redraw the table to show all data
                table.draw();
            });
        });
</script>
@endpush