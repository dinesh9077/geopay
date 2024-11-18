<?php
	
	namespace App\View\Components;
	
	use Closure;
	use Illuminate\Contracts\View\View;
	use Illuminate\View\Component;
	
	class DeleteModal extends Component
	{
		public $actionUrl;
		public $message;
		
		public function __construct($actionUrl = '', $message = 'Are you sure you want to delete this item?')
		{
			$this->actionUrl = $actionUrl;
			$this->message = $message;
		}
		
		public function render()
		{
			return view('components.delete-modal');
		}
	}
