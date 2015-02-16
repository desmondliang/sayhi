//This script deletes a member and the member's activity in the system. 
//@mid: member_id

Set @mid = [MID];//replace [MID] with the actual member_id in table.member.member_id 

SET SQL_SAFE_UPDATES = 0;
Delete From profile_app.response Where member_id = @mid;
Delete From profile_app.checkin Where member_id = @mid;
Delete From profile_app.membership Where member_id = @mid;
Delete From profile_app.member Where member_id = @mid;
SET SQL_SAFE_UPDATES = 1;