<?php
/**
 * Data available
 *
 * $campaign -> $campaign_id
 * 			 -> $campaign_name
 * 			 -> $alias
 * 			 -> getURL() 	Public URL to the campaign
 * 			 -> getMembers() A set of data objects that represent members signed in to this campaign.
 * 				-> firstname
 * 				-> lastname
 * 				-> member_id
 * 				-> last_checkin
 * 				-> mail
 * 			-> getRecentCheckIns() A set of data objects that represent most recent checkin history.
 *
 * $organization -> $name;
 */
?>

<section class="panel panel-primary">
  <div class="panel-body">

	<table id="campaign_info" data-campaign-id="<?php echo $campaign->campaign_id; ?>" class="table table-hover">
	  <thead>
	  	<tr>
		  <th>Campaign Name</th>
		  <th>Organization</th>
		  <th>URL</th>
	  	</tr>
	  </thead>
	  <tbody>
	  	<tr>
		  	<td><?php echo $campaign->campaign_name; ?></td>
	  		<td><?php echo $organization->name; ?></td>
			<td><a href="<?php echo $campaign->getURL(); ?>" target="_blank"><span class="badge"><?php echo $campaign->getURL(); ?></span></a></td>
		</tr>
	  </tbody>
	</table>
  </div>
</section>

<section id="charts" class="row">
  <div class="col-xs-12">
	<h3 class="text-center">New Visitors Trends</h3>
	<div class="chart_wrapper space_medium top bottom">
	  <canvas id="chartNewMembersTrends" width="400" height="100"></canvas>
	</div>

	<h3 class="text-center">Check-ins Trends</h3>
	<div class="chart_wrapper space_medium top bottom">
	  <canvas id="chartCheckInTrends" width="400" height="100"></canvas>
	</div>
  </div>
</section>

<section class="space_medium top bottom">
	<!-- Nav tabs -->
	<ul class="nav nav-tabs" role="tablist">
	  <li role="presentation" class="active"><a href="#members-container" role="tab" data-toggle="tab">Visitors <span class="badge"><?php echo $campaign->num_members; ?></span></a></li>
	  <li role="presentation"><a href="#checkins-container" role="tab" data-toggle="tab">Check-ins <span class="badge"><?php echo $campaign->num_checkins; ?></span></a></li>
	</ul>

	<!-- Tab panes -->
	<div class="tab-content">
	  <div role="tabpanel" class="tab-pane active" id="members-container">

		<table id="list-members" class="table table-hover">
		  <thead>
		  <tr>
			<th>Name</th>
			<th>Email</th>
			<th>Last Check In</th>
		  </tr>
		  </thead>

		  <tbody>
			<tr><td><i class="fa fa-refresh fa-spin fa-3x"></i></td></tr>
		  </tbody>
		</table>
	  </div>


	  <div role="tabpanel" class="tab-pane" id="checkins-container">

		<table id="list-checkins" class="table table-hover">
		  <thead>
		  <tr>
			<th>Name</th>
			<th>Email</th>
			<th>Last Check In</th>
		  </tr>
		  </thead>

		  <tbody>
		  <tr><td><i class="fa fa-refresh fa-spin fa-3x"></i></td></tr>
		  </tbody>

		</table>
	  </div>
	</div>
</section>