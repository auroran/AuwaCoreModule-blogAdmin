<?php
	class blogAdminModuleAdmin extends Auwa\Module{
		public function __construct(){
			$this->name = 'blogAdmin';
			$this->author = 'Grégory GAUDIN';
			$this->_path = _SYS_MOD_DIR_.'blogAdmin/';
			return $this->e;
		}
	}
?>