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
<div class="row">
  <section id="member-info" class="panel panel-primary col-sm-3" data-member-id="<?php echo $member->member_id; ?>">
	<div class="panel-body">
	  	<h3>Member Profile</h3>
		<p><i class="fa fa-user"></i>  <?php echo $member->firstname; ?> <?php echo $member->lastname; ?></p>
	  	<p><i class="fa fa-envelope"></i> <?php echo $member->email;?></p>
	</div>
  </section>

  <section class="space_medium top bottom col-sm-9">
	<!-- Nav tabs -->
	<ul class="nav nav-tabs">
	  <li class="active"><a href="#responses-container" role="tab" data-toggle="tab">Responses <span class="badge"></span></a></li>
	  <li><a href="#checkins-container" role="tab" data-toggle="tab">Check-ins <span class="badge"></span></a></li>
	</ul>

	<!-- Tab panes -->
	<div class="tab-content">
	  <div class="tab-pane fade in active" id="responses-container">
		<table id="list-responses" class="table table-hover">
		  <thead>
		  <tr class="row">
			<th class="col-xs-6">Question</th>
			<th class="col-xs-3">Response</th>
			<th class="col-xs-3">Date & Time</th>
		  </tr>
		  </thead>

		  <tbody>
		  <tr><td><i class="fa fa-refresh fa-spin fa-3x"></i></td></tr>
		  </tbody>
		</table>
	  </div>


	  <div class="tab-pane fade" id="checkins-container">
		<table id="list-checkins" class="table table-hover">
		  <thead>
		  <tr class="row">
			<th class="col-xs-1">Nth</th>
			<th class="col-xs-11">Date & Time</th>
		  </tr>
		  </thead>

		  <tbody>
		  <tr><td><i class="fa fa-refresh fa-spin fa-3x"></i></td></tr>
		  </tbody>
		</table>
	  </div>
	</div>
  </section>
</div>



