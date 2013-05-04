<?php
class Pagination{
	
	public $current_page;
	public $per_page;
	public $total_count;
	

	public function __construct($current_page = 1, $per_page = 5, $total_count = 0){
		$this->current_page     = (int)$current_page;
		$this->per_page = (int)$per_page;
		$this->total_count       =(int) $total_count;
		}
	//end construct
	

	public function previous_page(){
		return $this->current_page - 1;
		}
	//get_previous_post
	

	public function next_page(){
		return $this->current_page + 1;
		}
	//get_next_page
	
	
	public function offset(){
		return ($this->current_page - 1)* $this->per_page;
		}
	//offset
	
	public function total_page(){
		return ceil($this->total_count/$this->per_page);
		}
	//total_page
	
	public function has_previous_page(){
		if($this->previous_page() >= 1){
			return true;
			}else{
			return false;
			}
		}
	//has_previous_page
	
	public function has_next_page(){
		return $this->next_page() <= $this->total_page() ? true : false;
		}
	//has_next_page
	
		
		
	
	}//class end here
?>