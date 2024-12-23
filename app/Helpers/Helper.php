<?php  
	namespace App\Helpers;
	
	use App\Models\{
		LoginLog
	}; 
	use Jenssegers\Agent\Agent;
	use Illuminate\Support\Str;
	use Auth, DB, DateTime, Config;
	use Spatie\Activitylog\Models\Activity;  
	
	class Helper
	{  
		public static  function posted($time)
		{
			// Calculate difference between current
			// time and given timestamp in seconds
			$time = strtotime($time);
			$diff     = time() - $time;
			
			// Time difference in seconds
			$sec     = $diff;
			
			// Convert time difference in minutes
			$min     = round($diff / 60 );
			
			// Convert time difference in hours
			$hrs     = round($diff / 3600);
			
			// Convert time difference in days
			$days     = round($diff / 86400 );
			
			// Convert time difference in weeks
			$weeks     = round($diff / 604800);
			
			// Convert time difference in months
			$mnths     = round($diff / 2600640 );
			
			// Convert time difference in years
			$yrs     = round($diff / 31207680 );
			
			// Check for seconds
			if($sec <= 60) {
				$ret = "$sec seconds ago";
			}
			
			// Check for minutes
			else if($min <= 60) {
				if($min==1) {
					$ret =  "one minute ago";
				}
				else {
					$ret =  "$min minutes ago";
				}
			}
			
			// Check for hours
			else if($hrs <= 24) {
				if($hrs == 1) { 
					$ret =  "an hour ago";
				}
				else {
					$ret =  "$hrs hours ago";
				}
			}
			
			// Check for days
			else if($days <= 7) {
				if($days == 1) {
					$ret =  "Yesterday";
				}
				else {
					$ret =  "$days days ago";
				}
			}
			
			// Check for weeks
			else if($weeks <= 4.3) {
				if($weeks == 1) {
					$ret =  "a week ago";
				}
				else {
					$ret =  "$weeks weeks ago";
				}
			}
			
			// Check for months
			else if($mnths <= 12) {
				if($mnths == 1) {
					$ret =  "a month ago";
				}
				else {
					$ret =  "$mnths months ago";
				}
			}
			
			// Check for years
			else {
				if($yrs == 1) {
					$ret =  "one year ago";
				}
				else {
					$ret =  "$yrs years ago";
				}
			}
			
			return $ret;
		}
		
		public static function dateDiffInDays($date1, $date2) 
		{ 
			$diff = strtotime($date2) - strtotime($date1);  
			return abs(round($diff / 86400))+1;
		}
		
		public static function formatNumber($number) 
		{
			$formattedNumber = '';
			
			if ($number >= 10000000) 
			{
				$formattedNumber = ($number / 10000000) . ' Cr';
			} 
			elseif ($number >= 100000)
			{
				$formattedNumber = ($number / 100000) . ' Lac';
			} 
			elseif ($number >= 1000) 
			{
				$formattedNumber = ($number / 1000) . ' K';
			} 
			else 
			{
				$formattedNumber = $number;
			} 
			return $formattedNumber;
		}
		
		public static function AmountInWords(float $amount)
		{
			$amount_after_decimal = round($amount - ($num = floor($amount)), 2) * 100;
			// Check if there is any number after decimal
			$amt_hundred = null;
			$count_length = strlen($num);
			$x = 0;
			$string = array();
			$change_words = array(0 => '', 1 => 'One', 2 => 'Two',
			3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six',
			7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
			10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve',
			13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
			16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
			19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty',
			40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty',
			70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety');
			$here_digits = array('', 'Hundred','Thousand','Lakh', 'Crore');
			while( $x < $count_length ) {
				$get_divider = ($x == 2) ? 10 : 100;
				$amount = floor($num % $get_divider);
				$num = floor($num / $get_divider);
				$x += $get_divider == 10 ? 1 : 2;
				if ($amount) {
					$add_plural = (($counter = count($string)) && $amount > 9) ? 's' : null;
					$amt_hundred = ($counter == 1 && $string[0]) ? ' and ' : null;
					$string [] = ($amount < 21) ? $change_words[$amount].' '. $here_digits[$counter]. $add_plural.' 
					'.$amt_hundred:$change_words[floor($amount / 10) * 10].' '.$change_words[$amount % 10]. ' 
					'.$here_digits[$counter].$add_plural.' '.$amt_hundred;
				}
				else $string[] = null;
			}
			$implode_to_Rupees = implode('', array_reverse($string));
			$get_paise = ($amount_after_decimal > 0) ? "And " . ($change_words[$amount_after_decimal / 10] . " 
			" . $change_words[$amount_after_decimal % 10]) . '' : '';
			return ($implode_to_Rupees ? $implode_to_Rupees . '' : '') . $get_paise;
		}
		
		public static function decimal_number($number,$decimal=null)
		{
			if(empty($decimal))
			{
				$decimal = 2;
			}
			//return number_format($number,$decimal);
			
			$decimal = (string)($number - floor($number));
			$money = floor($number);
			$length = strlen($money);
			$delimiter = '';
			$money = strrev($money);
			
			for($i=0;$i<$length;$i++){
				if(( $i==3 || ($i>3 && ($i-1)%2==0) )&& $i!=$length){
					$delimiter .=',';
				}
				$delimiter .=$money[$i];
			}
			
			$result = strrev($delimiter);
			$decimal = preg_replace("/0\./i", ".", $decimal);
			$decimal = substr($decimal, 0, 3);
			
			if( $decimal != '0'){
				$result = $result.$decimal;
			}
			
			return $result;
			
		}
		
		public static function decimalsprint($number, $decimal=0)
		{
			return sprintf("%0.".$decimal."f", $number);
		}
		  
		public static function loginLog($type, $user, $source = "WEB")
		{    
			$ip = request()->ip(); 
			$agent = new Agent();

			$device = $agent->isDesktop() ? 'Desktop' : ($agent->isTablet() ? 'Tablet' : 'Mobile');
			$browser = $agent->browser(); 

			$currentDate = now()->toDateString(); // Get only the date
			
			// Use updateOrCreate for logging
			LoginLog::updateOrCreate(
				[
					'user_id' => $user->id,
					'source' => $source,
					'created_at' => $currentDate, // Match only the date part
				],
				[
					'type' => $type,
					'ip_address' => $ip,
					'device' => $device,
					'browser' => $browser,
					'updated_at' => now(),
				]
			);
		}
		   
		public static function saveData($object,$data)
		{
			$object->fillable(array_keys($data));	
			$object->fill($data);  
			$object->save(); 
		}
		
		public static function updateLogName($subjectId = null, $subject_type, $log_name = NULL, $module_id = NULL, $log = NULL)
		{ 
			$latestActivityLog = Activity::where('subject_id', $subjectId)->where('subject_type', $subject_type)->latest()->first(); 
			
			if($latestActivityLog) 
			{ 
				$latestActivityLog->module_id = $module_id; 
				$latestActivityLog->log_name = $log_name; 
				if($log != null)
				{
					$latestActivityLog->description = $log; 
				}
				$latestActivityLog->save();
			} 
		}
	}																		