<?php
/**
*@author  				Jeremiah Iro
*@email   				jeremiahiro@gmail.com
*@website 				www.irotech.com.ng
*@version               1.0
**/
class Paginator {		
	public $itemsPerPage;
	public $range;
	public $currentPage;
	public $total;
	public $textNav;
	public $link;
	private $_navigation;		
	private $_pageNumHtml;
	private $_itemHtml;
	private $_searchInput;
	/**
	 * Constructor
	 */
	public function __construct()
	{
		//set default values
		$this->itemsPerPage = 10;
		$this->range        = 5;
		$this->search       = '';
		$this->currentPage  = 1;		
		$this->total		= 0;
		$this->textNav 		= false;
		$this->itemSelect   = array(10,20,50,100,'All');	

		//private values
		$this->_navigation  = array(
				'next'=>'Next',
				'pre' =>'Pre',
				'ipp' =>'Item per page'
		);

		// $this->link 		 = filter_var($_SERVER['PHP_SELF'], FILTER_UNSAFE_RAW);
		$this->_pageNumHtml  = '';
		$this->_itemHtml 	 = '';
		$this->_searchInput 	 = '';
	}
	
	/**
	 * paginate main function
	 * 
	 * @access              public
	 * @return              type
	 */
	public function paginate()
	{
		//get current page
		if(isset($_GET['current'])){
			$this->currentPage  = $_GET['current'];		
		}			
		//get item per page
		if(isset($_GET['item'])){
			$this->itemsPerPage = $_GET['item'];
		}
		
		//get search value
		if(isset($_GET['search'])){
			$this->search = $_GET['search'];
		}

		//get page numbers
		$this->_pageNumHtml = $this->_getPageNumbers();	

		//get item per page select box
		$this->_itemHtml	= $this->_getItemSelect();	

		//get search input
		$this->_searchInput	= $this->_getSearchInput();	
	}
			
	/**
	 * return pagination numbers in a format of UL list
	 * 
	 * @access              public
	 * @param               type $parameter
	 * @return              string
	 */
	public function pageNumbers()
	{
		if(empty($this->_pageNumHtml)){
			exit('Please call function paginate() first.');
		}
		return $this->_pageNumHtml;
	}
	
	/**
	 * return jump menu in a format of select box
	 *
	 * @access              public
	 * @return              string
	 */
	public function itemsPerPage()
	{          
		if(empty($this->_itemHtml)){
			exit('Please call function paginate() first.');
		}
		return $this->_itemHtml;	
	} 

	/**
	 * return search input
	 *
	 * @access              public
	 * @return              string
	 */
	public function itemsSearch()
	{          
		if(empty($this->_searchInput)){
			exit('Please call function paginate() first.');
		}
		return $this->_searchInput;	
	} 
	
	/**
	 * return page numbers html formats
	 *
	 * @access              public
	 * @return              string
	 */
	private function  _getPageNumbers()
	{
		$html  = '<ul style="list-style: none; width: 100px; display:flex; justify-content: space-around;">'; 
		
		//previous link button
		if($this->textNav && ($this->currentPage > 1)){
			$html .= '<li><a style="text-decoration:none; color: #0000FF;" href="'.$this->link .'?current='.($this->currentPage-1).'"';
			$html .= '>'.$this->_navigation['pre'].'</a></li>';
		}        	
		//do ranged pagination only when total pages is greater than the range
		if($this->total > $this->range){	
			// die($this->currentPage);
			$start = ($this->currentPage <= $this->range) ? 1 : ($this->currentPage - $this->range);
			$end   = (($this->total - $this->currentPage) >= $this->range) ? ($this->currentPage + $this->range) : $this->total;
		}else{
			$start = 1;
			$end   = $this->total;
		}    

		//loop through page numbers
		for($i = $start; $i <= $end; $i++){
			$html .= '<li><a href="'.$this->link .'?current='.$i.'"';
			$i == $this->currentPage ? $html .= "style='text-decoration:none; color: #FF0000'" : $html .= "style='text-decoration:none; color: #0000FF;'";
			$html .= '>'.$i.'</a></li>';
		}        	

		//next link button
		if($this->textNav && ($this->currentPage < $this->total)){
			$html .= '<li><a style="text-decoration:none; color: #0000FF;" href="'.$this->link .'?current='.($this->currentPage+1).'"';
			$html .= '>'.$this->_navigation['next'].'</a></li>';
		}
		$html .= '</ul>';

		return $html;
	}
	
	/**
	 * return item select box
	 *
	 * @access              public
	 * @return              string
	 */
	private function  _getItemSelect()
	{
		$items = '';
		$ippArray = $this->itemSelect;   			
		foreach($ippArray as $ippOpt){   
			$items .= ($ippOpt == $this->itemsPerPage) ? "<option selected value=\"$ippOpt\">$ippOpt</option>\n":"<option value=\"$ippOpt\">$ippOpt</option>\n";
		}   			
		return "<span class=\"paginate\">".$this->_navigation['ipp']."</span>
		<select class=\"paginate\" onchange=\"window.location='$this->link?current=1&item='+this[this.selectedIndex].value;return false\">$items</select>\n";   	
	}

	/**
	 * return search input
	 *
	 * @access              public
	 * @return              string
	 */
	private function  _getSearchInput()
	{
		$input = "<div style='padding: 10px;'></div><label> Search: </label></br>";
		$input .= "<input type=\"search\" placeholder=\"search...\" value='$this->search' oninput=\"setTimeout((e) => {window.location='$this->link?current=1&item=$this->itemsPerPage&search='+this.value;return false}, 1000)\" />";
		$input .= "</div></br>";

		return $input;
	}
}