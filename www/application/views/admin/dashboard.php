<?php
/**
 * Data available
 *
 * $campaigns array a list of campaign dataset object
 * 		$campaign->campaign_id;
 * 		$campaign->alias;
 * 		$campaign->campaign_name;
 * 		$campaign->org_id;
 * 		$campaign->created_datetime;
 * 		$campaign->num_members;
 */
?>

	  <h1>Dashboard</h1>	  
	  <h2>Campaigns</h2>

  <div class="clearfix">
	<table id="campaigns" class="table table-hover">
	  <thead>
	  	<tr>
	  	<th>Campaign Name</th>
	  	<th>Members</th>
	  	<th></th>
		</tr>
	  </thead>
		<?php
			if(sizeof($campaigns) > 0){
			  foreach($campaigns as $campaign){
		?>
			  <tr class="campaign" data-campaign_id="<?php echo $campaign->campaign_id; ?>">
				<td class="col-sm-9"><?php echo $campaign->campaign_name; ?></td>
				<td class="col-sm-1"><a href="/admin/campaign/<?php echo $campaign->campaign_id; ?>" class="btn btn-default"><i class="fa fa-users"></i> <?php echo $campaign->num_members; ?></a></td>
				<td class="col-sm-2"><a href="/admin/campaign/<?php echo $campaign->campaign_id; ?>" class="btn-default btn" data-campaign_id="<?php echo $campaign->campaign_id; ?>"><i class="fa fa-cog"></i> Settings</a></td>
			  </tr>
		<?php
			  }//foreach
			}else{//no campaigns owned by this user.
		?>
			  <tr class="campaign">
				<td class="col-sm-10">You haven't created any campaign.</td>
				<td class="col-sm-2"><a href="#" class="btn-default btn">New Campaign</a></td>
			  </tr>
	  	<?php
			}//if
		?>
	</ul>
  </table>
