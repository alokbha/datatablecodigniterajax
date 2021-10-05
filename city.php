------------Controller CODE------------



public function city_list(){
		$this->load->view('admin/header');
		$this->load->view('admin/city');
		$this->load->view('admin/footer');
	}
	public function city_ajax(){
		$list = $this->Admin_model->get_datatables_city();
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $city){
			$no++;
			$row = array();
			$row[] = $no;
			$row[] = $city->city_name;
            $row[] = $city->sname;
            $row[] = $city->country_name;
            $row[] = '<div class="d-flex align-items-center card-action-wrap">
            <div class="inline-block dropdown">
            <a class="dropdown-toggle no-caret" data-toggle="dropdown" href="#" aria-expanded="false" role="button"><i class="ion ion-ios-more"></i></a>
            <div class="dropdown-menu dropdown-menu-right" x-placement="top-end" style="position: absolute; transform: translate3d(-214px, -161px, 0px); top: 0px; left: 0px; will-change: transform;">
            <a class="dropdown-item" href="'.base_url().'Adminhome/city_edit/'.$city->city_id.'" ><i class="fas fa-edit read-icon"></i> Edit</a>
            <a id="mybutton" href="javascript:void(0);" onclick="citydelete('.$city->city_id.')" class="dropdown-item text-danger remove"  data-toggle="modal" data-target="#delete" data-id="'.$city->city_id.'"><i class="fas fa-trash-alt read-icon text-danger"></i> Delete</a>
            </div>
            </div>
            </div>';
            $data[] = $row;
        }
        $output = array(
        	"draw" => $_POST['draw'],
        	"recordsTotal" => $this->Admin_model->count_all_city(),
        	"recordsFiltered" => $this->Admin_model->count_filtered_city(),
        	"data" => $data,
        );
        echo json_encode($output);
    }
	public function add_new_city(){
	$data['country'] = $this->Admin_model->fetch_country();
	$this->load->view('admin/header',$data);
	$this->load->view('admin/city-add');
	$this->load->view('admin/footer');
	}

	function fetch_state()
	{
	if($this->input->post('country_id'))
	{
	echo $this->Admin_model->fetch_state($this->input->post('country_id'));
	}
	}




	----------------MODEL CODE HERE ----------




	private function get_datatables_query_city()
    {

       $column_search = array('cities.name','states.name','countries.country_name');
         $order = array('city_id' => 'desc');
         $column_order = array(null, 'city_name', 'sname', 'country_name', null);
       $this->db->select('cities.city_id,cities.name as city_name,states.name as sname,states.id as state_id,countries.id as country_id,countries.country_name');
  $this->db->from('cities');
  $this->db->join('states','states.id=cities.state_id','left');
  $this->db->join('countries','countries.id=states.country_id','left');
        $i = 0;
     
        foreach ($column_search as $item) // loop column 
        {
            if($_POST['search']['value']) // if datatable send POST for search
            {
                 
                if($i===0) // first loop
                {
                    $this->db->group_start();
                    $this->db->like($item, $_POST['search']['value']);
                }
                else
                {
                    $this->db->or_like($item, $_POST['search']['value']);
                }
 
                if(count($column_search) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
        }
         
        if(isset($_POST['order'])) // here order processing
        {
            $this->db->order_by($column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } 
        else if(isset($order))
        {
            $order = $order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }
 
    function get_datatables_city()
    {
        $this->get_datatables_query_city();
        if($_POST['length'] != -1)
        $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }
 
    function count_filtered_city()
    {
        $this->get_datatables_query_city();
        $query = $this->db->get();
        return $query->num_rows();
    }
 
    public function count_all_city()
    {
        $this->db->from('cities');
        return $this->db->count_all_results();
    }


-----------  VIEW FILE Code HERE----



            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="example">
                                        <thead>
                                            <tr>
                                                <th>Sr.Number</th>
                                                <th>City Name</th>
                                                <th>State Name</th>
                                                <th>Country</th>
                                                <th>Manage</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <div class="modal fade" id="delete" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    Are you sure want to delete.
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <a id="confirm-button"> <button type="button" class="btn btn-danger">Delete</button></a>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    <div class="alert alert-primary show2" role="alert" id="snackbar2" style="visibility: hidden">
        Successfully Deleted
    </div>
    <script>
        function citydelete(id){
            $('#delete').modal('show');
            rowToDelete = $(this).closest('tr');
            $('#confirm-button').click(function(){
                 $.ajax({
                    url: '<?php echo base_url();?>/Adminhome/city_delete',
                    type: 'GET',
                    data: {id: id},
                    success: function (data){
                        $("#"+id).remove();
                        var table = $('#example').DataTable();
                        table.ajax.reload( null, false );
                        $("#delete").modal('hide');
                        document.getElementById("snackbar2").style.visibility = "visible";
                        setTimeout(function() {
                            document.getElementById('snackbar2').style.visibility = 'hidden';
                        }, 3000);
                    }
                });
                })
        }
    </script>
    <script type="text/javascript">
        var table;
        $(document).ready(function() {
            table = $('#example').DataTable({ 
                "processing": true,
                "serverSide": true,
                "stateSave": true,
                "order": [],
                "ajax": {
                    "url": "<?php echo site_url('Adminhome/city_ajax')?>",
                    "type": "POST"
                },
                "columnDefs": [
                { 
                    "targets": [ 0 ],
                    "orderable": false,
                },
                ],
            });
        });
    </script>  
