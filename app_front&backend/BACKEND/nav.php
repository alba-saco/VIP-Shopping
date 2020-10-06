<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: authorization, content-type, x-requested-with');
require_once __DIR__ . '/config.php';

// Takes raw data from the request
$json = file_get_contents('php://input');
$post_node = json_decode($json);

//classes
class Coord {
    public function __construct($no, $x, $y){
        $this->no = $no;
        $this->x = $x;
        $this->y = $y;
    }
}

class Transition {
    public function __construct($trans_no, $trans_coord){
        $this->trans_no = $trans_no;
        $this->trans_coord = $trans_coord;
    }
}

class Adjacency {
    public function __construct($adj_id, $start_room, $end_room, $transition, $trans_orient){
        $this->adj_id = $adj_id;
        $this->start_room = $start_room;
        $this->end_room = $end_room;
        $this->transition = $transition;
        $this->trans_orient = $trans_orient;
    }

}

class Room {
    public function __construct($room_no, $room_name, $coords, $adjacencies, $transitions, $nav_area){
        $this->room_no = $room_no;
        $this->room_name = $room_name;
        $this->coords = $coords;
        $this->adjacencies = $adjacencies;
        $this->transitions = $transitions;
        $this->nav_area = $nav_area;
    }
}

class Graph
{
  protected $graph;
  protected $visited = array();

  public function __construct($graph) {
    $this->graph = $graph;
  }


  //breadth first search algorithm: https://www.sitepoint.com/data-structures-4/
  // find least number of hops (edges) between 2 nodes
  // (vertices)
  public function breadthFirstSearch($origin, $destination) {
    // mark all nodes as unvisited
    foreach ($this->graph as $vertex => $adj) {
      $this->visited[$vertex] = false;
    }

    // create an empty queue
    $q = new SplQueue();

    // enqueue the origin vertex and mark as visited
    $q->enqueue($origin);
    $this->visited[$origin] = true;

    // this is used to track the path back from each node
    $path = array();
    $path[$origin] = new SplDoublyLinkedList();
    $path[$origin]->setIteratorMode(
      SplDoublyLinkedList::IT_MODE_FIFO|SplDoublyLinkedList::IT_MODE_KEEP
    );

    $path[$origin]->push($origin);

    $found = false;
    // while queue is not empty and destination not found
    while (!$q->isEmpty() && $q->bottom() != $destination) {
      $t = $q->dequeue();

      if (!empty($this->graph[$t])) {
        // for each adjacent neighbor
        foreach ($this->graph[$t] as $vertex) {
          if (!$this->visited[$vertex]) {
            // if not yet visited, enqueue vertex and mark
            // as visited
            $q->enqueue($vertex);
            $this->visited[$vertex] = true;
            // add vertex to current path
            $path[$vertex] = clone $path[$t];
            $path[$vertex]->push($vertex);
          }
        }
      }
    }

    if (isset($path[$destination])) {
        return $path[$destination];
    }
    else {
      echo "No route from $origin to $destination";
    }
  }
}

//functions
function get_orientation($x_direction, $y_direction){
    global $orientation;
    $direction = array();
    if ($orientation == 'N') {
        if ($x_direction > 0){
            $x_orientation = "right";
            if ($y_direction > 0){
                $y_orientation = "left";
            } elseif ($y_direction < 0){
                $y_orientation = "right";
            }
        } elseif ($x_direction < 0) {
            $x_orientation = "left";
            if ($y_direction > 0){
                $y_orientation = "right";
            } elseif ($y_direction < 0){
                $y_orientation = "left";
            }
        } elseif ($x_direction == 0){
            if ($y_direction > 0){
                $y_orientation = "straight ahead";
            } elseif ($y_direction < 0){
                $y_orientation = "after turning 180 degrees";
            }
        }
    } elseif ($orientation == 'E') {
        if ($x_direction > 0){
            $x_orientation = "straight ahead";
            if ($y_direction > 0){
                $y_orientation = "left";
            } elseif ($y_direction < 0){
                $y_orientation = "right";
            }
        } elseif ($x_direction < 0) {
            $x_orientation = "after turning 180 degrees";
            if ($y_direction > 0){
                $y_orientation = "right";
            } elseif ($y_direction < 0){
                $y_orientation = "left";
            }
        } elseif ($x_direction == 0){
            if ($y_direction > 0){
                $y_orientation = "after turning left";
            } elseif ($y_direction < 0){
                $y_orientation = "after turning right";
            }
        }
    } elseif ($orientation == 'S') {
        if ($x_direction > 0){
            $x_orientation = "left";
            if ($y_direction > 0){
                $y_orientation = "left";
            } elseif ($y_direction < 0){
                $y_orientation = "right";
            }
        } elseif ($x_direction < 0) {
            $x_orientation = "right";
            if ($y_direction > 0){
                $y_orientation = "right";
            } elseif ($y_direction < 0){
                $y_orientation = "left";
            }
        } elseif ($x_direction == 0){
            if ($y_direction > 0){
                $y_orientation = "after turning 180 degrees";
            } elseif ($y_direction < 0){
                $y_orientation = "straight ahead";
            }
        }
    } elseif ($orientation == 'W') {
        if ($x_direction > 0){
            $x_orientation = "after turning 180 degrees";
            if ($y_direction > 0){
                $y_orientation = "left";
            } elseif ($y_direction < 0){
                $y_orientation = "right";
            }
        } elseif ($x_direction < 0) {
            $x_orientation = "straight ahead";
            if ($y_direction > 0){
                $y_orientation = "right";
            } elseif ($y_direction < 0){
                $y_orientation = "left";
            }
        } elseif ($x_direction == 0){
            if ($y_direction > 0){
                $y_orientation = "after turning right";
            } elseif ($y_direction < 0){
                $y_orientation = "after turning left";
            }
        }
    }
    if(empty($x_orientation)){
        $x_orientation = 0;
    } elseif(empty($y_orientation)){
        $y_orientation = 0;
    }
    $direction[0] = $x_orientation;
    $direction[1] = $y_orientation;
    return $direction;
}

function get_orientation_y($x_direction, $y_direction){
    global $orientation;
    $direction = array();
    if ($orientation == 'N') {
        if ($y_direction > 0){
            $y_orientation = "straight ahead";
            if ($x_direction > 0){
                $x_orientation = "right";
            } elseif ($x_direction < 0){
                $x_orientation = "left";
            }
        } elseif ($y_direction < 0) {
            $y_orientation = "after turning 180 degrees";
            if ($x_direction > 0){
                $x_orientation = "left";
            } elseif ($x_direction < 0){
                $x_orientation = "right";
            }
        } elseif ($y_direction == 0){
            if ($x_direction > 0){
                $x_orientation = "right";
            } elseif ($x_direction < 0){
                $x_orientation = "left";
            }
        }
    } elseif ($orientation == 'E') {
        if ($y_direction > 0){
            $y_orientation = "left";
            if ($x_direction > 0){
                $x_orientation = "right";
            } elseif ($x_direction < 0){
                $x_orientation = "left";
            }
        } elseif ($y_direction < 0) {
            $y_orientation = "right";
            if ($x_direction > 0){
                $x_orientation = "left";
            } elseif ($x_direction < 0){
                $x_orientation = "right";
            }
        } elseif ($y_direction == 0){
            if ($x_direction > 0){
                $x_orientation = "straight ahead";
            } elseif ($x_direction < 0){
                $x_orientation = "after turning 180 degrees";
            }
        }
    } elseif ($orientation == 'S') {
        if ($y_direction > 0){
            $y_orientation = "after turning 180 degrees";
            if ($x_direction > 0){
                $x_orientation = "right";
            } elseif ($x_direction < 0){
                $x_orientation = "left";
            }
        } elseif ($y_direction < 0) {
            $y_orientation = "straight ahead";
            if ($x_direction > 0){
                $x_orientation = "left";
            } elseif ($x_direction < 0){
                $x_orientation = "right";
            }
        } elseif ($y_direction == 0){
            if ($x_direction > 0){
                $x_orientation = "left";
            } elseif ($x_direction < 0){
                $x_orientation = "right";
            }
        }
    } elseif ($orientation == 'W') {
        if ($y_direction > 0){
            $y_orientation = "right";
            if ($x_direction > 0){
                $x_orientation = "right";
            } elseif ($x_direction < 0){
                $x_orientation = "left";
            }
        } elseif ($y_direction < 0) {
            $y_orientation = "left";
            if ($x_direction > 0){
                $x_orientation = "left";
            } elseif ($x_direction < 0){
                $x_orientation = "right";
            }
        } elseif ($y_direction == 0){
            if ($x_direction > 0){
                $x_orientation = "after turning 180 degrees";
            } elseif ($x_direction < 0){
                $x_orientation = "straight ahead";
            }
        }
    }
    if(empty($x_orientation)){
        $x_orientation = 0;
    } elseif(empty($y_orientation)){
        $y_orientation = 0;
    }
    $direction[0] = $x_orientation;
    $direction[1] = $y_orientation;
    return $direction;
}

function get_instructions($trans, $current_location, $nav_area){
    global $orientation;
    $x_direction = $trans->transition->trans_coord->x - $current_location->x;
    $y_direction = $trans->transition->trans_coord->y - $current_location->y;
    if($x_direction < 0){
        $x_path = -1;
    } else {
        $x_path = 1;
    }
    if($y_direction < 0){
        $y_path = -1;
    } else {
        $y_path = 1;
    }
    $instructions = array();
    if(in_array(array($trans->transition->trans_coord->x, $current_location->y), $nav_area)){
        $direction = get_orientation($x_direction, $y_direction);
        $x_orientation = $direction[0];
        $y_orientation = $direction[1];
        $x_steps = round($x_direction/70);
        $y_steps = round($y_direction /70);
        if($x_steps == 0){
            $y_vector = get_orientation_y($x_direction, $y_direction);
            $y_orientation = $y_vector[1];
            $instruction = "Walk " . abs($y_steps) . " steps " . $y_orientation . ".";
        } elseif($y_steps == 0){
            $instruction = "Walk " . abs($x_steps) . " steps " . $x_orientation . ".";
        } else {
            $instruction = "Walk " . abs($x_steps) . " steps " . $x_orientation . " and then " . abs($y_steps) . " steps " . $y_orientation . ".";
        }
        array_push($instructions, $instruction);
    } else {
        while(($current_location->x != $trans->transition->trans_coord->x) || ($current_location->y != $trans->transition->trans_coord->y)){
            $xcoords = find_xcoords($current_location->y, $nav_area);
            if(in_array($trans->transition->trans_coord->x, $xcoords)){
                $xtravel = $trans->transition->trans_coord->x;
            } elseif($x_path < 0){
                $xtravel = min($xcoords);
            } elseif($x_path > 0){
                $xtravel = max($xcoords);
            }
            $x_direction = $xtravel - $current_location->x;
            if (abs($x_direction) > 70){
                $ycoords = find_ycoords($xtravel, $nav_area);
                if(in_array($trans->transition->trans_coord->y, $ycoords)){
                    $ytravel = $trans->transition->trans_coord->y;
                } elseif($y_path < 0){
                    $ytravel = min($ycoords);
                } elseif($y_path > 0){
                    $ytravel = max($ycoords);
                }
                $y_direction = $ytravel - $current_location->y;
                $direction = get_orientation($x_direction, $y_direction);
                $x_orientation = $direction[0];
                $y_orientation = $direction[1];
                $x_steps = round($x_direction/70);
                $y_steps = round($y_direction /70);
                if($x_steps == 0){
                    $y_vector = get_orientation_y($x_direction, $y_direction);
                    $y_orientation = $y_vector[1];
                    $instruction = "Walk " . abs($y_steps) . " steps " . $y_orientation . ".";
                } elseif($y_steps == 0){
                    $instruction = "Walk " . abs($x_steps) . " steps " . $x_orientation . ".";
                } else {
                $instruction = "Walk " . abs($x_steps) . " steps " . $x_orientation . " and then " . abs($y_steps) . " steps " . $y_orientation . ".";
                }
                facing_direction($x_direction, $y_direction, 0);
            } else {
                $ycoords = find_ycoords($current_location->x, $nav_area);
                if(in_array($trans->transition->trans_coord->y, $ycoords)){
                    $ytravel = $trans->transition->trans_coord->y;
                } elseif($y_path < 0){
                    $ytravel = min($ycoords);
                } elseif($y_path > 0){
                    $ytravel = max($ycoords);
                }
                $y_direction = $ytravel - $current_location->y;
                $xcoords = find_xcoords($ytravel, $nav_area);
                if(in_array($trans->transition->trans_coord->x, $xcoords)){
                    $xtravel = $trans->transition->trans_coord->x;
                } elseif($x_path < 0){
                    $xtravel = min($xcoords);
                } elseif($x_path > 0){
                    $xtravel = max($xcoords);
                }
                $x_direction = $xtravel - $current_location->x;
                $direction = get_orientation_y($x_direction, $y_direction);
                $x_orientation = $direction[0];
                $y_orientation = $direction[1];
                $x_steps = round($x_direction/70);
                $y_steps = round($y_direction /70);
                if($x_steps == 0){
                    $y_vector = get_orientation_y($x_direction, $y_direction);
                    $y_orientation = $y_vector[1];
                    $instruction = "Walk " . abs($y_steps) . " steps " . $y_orientation . ".";
                } elseif($y_steps == 0){
                    $instruction = "Walk " . abs($x_steps) . " steps " . $x_orientation . ".";
                } else {
                $instruction = "Walk " . abs($y_steps) . " steps " . $y_orientation . " and then " . abs($x_steps) . " steps " . $x_orientation . ".";
                }
                facing_direction($x_direction, $y_direction, 1);
            }
            array_push($instructions, $instruction);
            $current_location = new Coord(0, $xtravel, $ytravel);
        }
    }
    return $instructions;
}


function find_xcoords($ycoord, $nav_area){
    $xcoords = array();
    foreach ($nav_area as $coord){
        if($coord[1] == $ycoord){
            array_push($xcoords, $coord[0]);
        }
    }
    return $xcoords;
}

function find_ycoords($xcoord, $nav_area){
    $ycoords = array();
    foreach ($nav_area as $coord){
        if($coord[0] == $xcoord){
            array_push($ycoords, $coord[1]);
        }
    }
    return $ycoords;
};

function facing_direction($x_direction, $y_direction, $start){
   global $orientation;
    if ($start == 0){
        if ($y_direction > 0){
            $orientation = 'N';
        } elseif ($y_direction < 0){
            $orientation = 'S';
        }
    } elseif ($start == 1){
        if ($x_direction > 0){
            $orientation = 'E';
        } elseif ($x_direction < 0){
            $orientation = 'W';
        }
    }
}

function same_room_instruction($current_location, $desired_location, $nav_area){
    global $orientation;
    $x_direction = $desired_location->x - $current_location->x;
    $y_direction = $desired_location->y - $current_location->y;
    if($x_direction < 0){
        $x_path = -1;
    } else {
        $x_path = 1;
    }
    if($y_direction < 0){
        $y_path = -1;
    } else {
        $y_path = 1;
    }
    $instructions = array();
    if(in_array(array($desired_location->x, $current_location->y), $nav_area)){
        $direction = get_orientation($x_direction, $y_direction);
        $x_orientation = $direction[0];
        $y_orientation = $direction[1];
        $x_steps = round($x_direction/70);
        $y_steps = round($y_direction /70);
        if($x_steps == 0){
            $y_vector = get_orientation_y($x_direction, $y_direction);
            $y_orientation = $y_vector[1];
            $instruction = "Walk " . abs($y_steps) . " steps " . $y_orientation . ".";
        } elseif($y_steps == 0){
            $instruction = "Walk " . abs($x_steps) . " steps " . $x_orientation . ".";
        } else {
            $instruction = "Walk " . abs($x_steps) . " steps " . $x_orientation . " and then " . abs($y_steps) . " steps " . $y_orientation . ".";
        }
        array_push($instructions, $instruction);
    } else {
        while(($current_location->x != $desired_location->x) || ($current_location->y != $desired_location->y)){
            $xcoords = find_xcoords($current_location->y, $nav_area);
            if(in_array($desired_location->x, $xcoords)){
                $xtravel = $desired_location->x;
            } elseif($x_path < 0){
                $xtravel = min($xcoords);
            } elseif($x_path > 0){
                $xtravel = max($xcoords);
            }
            $x_direction = $xtravel - $current_location->x;
            if (abs($x_direction) > 70){
                $ycoords = find_ycoords($xtravel, $nav_area);
                if(in_array($desired_location->y, $ycoords)){
                    $ytravel = $desired_location->y;
                } elseif($y_path < 0){
                    $ytravel = min($ycoords);
                } elseif($y_path > 0){
                    $ytravel = max($ycoords);
                }
                $y_direction = $ytravel - $current_location->y;
                $direction = get_orientation($x_direction, $y_direction);
                $x_orientation = $direction[0];
                $y_orientation = $direction[1];
                $x_steps = round($x_direction/70);
                $y_steps = round($y_direction /70);
                if($x_steps == 0){
                    $y_vector = get_orientation_y($x_direction, $y_direction);
                    $y_orientation = $y_vector[1];
                    $instruction = "Walk " . abs($y_steps) . " steps " . $y_orientation . ".";
                } elseif($y_steps == 0){
                    $instruction = "Walk " . abs($x_steps) . " steps " . $x_orientation . ".";
                } else {
                    $instruction = "Walk " . abs($x_steps) . " steps " . $x_orientation . " and then " . abs($y_steps) . " steps " . $y_orientation . ".";
                }
                facing_direction($x_direction, $y_direction, 0);
            } else {
                $ycoords = find_ycoords($current_location->x, $nav_area);
                if(in_array($desired_location->y, $ycoords)){
                    $ytravel = $desired_location->y;
                } elseif($y_path < 0){
                    $ytravel = min($ycoords);
                } elseif($y_path > 0){
                    $ytravel = max($ycoords);
                }
                $y_direction = $ytravel - $current_location->y;
                $xcoords = find_xcoords($ytravel, $nav_area);
                if(in_array($desired_location->x, $xcoords)){
                    $xtravel = $desired_location->x;
                } elseif($x_path < 0){
                    $xtravel = min($xcoords);
                } elseif($x_path > 0){
                    $xtravel = max($xcoords);
                }
                $x_direction = $xtravel - $current_location->x;
                $direction = get_orientation_y($x_direction, $y_direction);
                $x_orientation = $direction[0];
                $y_orientation = $direction[1];
                $x_steps = round($x_direction/70);
                $y_steps = round($y_direction /70);
                if($x_steps == 0){
                    $y_vector = get_orientation_y($x_direction, $y_direction);
                    $y_orientation = $y_vector[1];
                    $instruction = "Walk " . abs($y_steps) . " steps " . $y_orientation . ".";
                } elseif($y_steps == 0){
                    $instruction = "Walk " . abs($x_steps) . " steps " . $x_orientation . ".";
                } else {
                    $instruction = "Walk " . abs($y_steps) . " steps " . $y_orientation . " and then " . abs($x_steps) . " steps " . $x_orientation . ".";
                }
                facing_direction($x_direction, $y_direction, 1);
            }
            array_push($instructions, $instruction);
            $current_location = new Coord(0, $xtravel, $ytravel);
        }
    }
    return $instructions;
}

function get_room($current_location){
    global $rooms;
    foreach($rooms as $room){
        if(in_array($current_location, $room->coords)){
            return $room;
        }
    }
}

function room_by_no($room_no){
    global $rooms;
    foreach($rooms as $room){
        if($room->room_no == $room_no){
            return $room;
        }
    }
}

function get_path($current_location, $desired_location, $g){
    global $orientation;
    $current_room = get_room($current_location);
    $desired_room = get_room($desired_location);
    $steps = $g->breadthFirstSearch($current_room->room_no, $desired_room->room_no);
    $instruction_set = array();
    if (count($steps) == 1){
        $nav_area = $current_room->nav_area;
        $temp_instr = same_room_instruction($current_location, $desired_location, $nav_area);
        array_push($instruction_set, $temp_instr);
    } else{
        for($i=1; $i < count($steps); $i++){
            $nav_area = $current_room->nav_area;
            $temp_desired = room_by_no($steps[$i]);
            foreach($current_room->adjacencies as $adj){
                if ($adj->end_room == $temp_desired->room_no){
                    $trans = $adj;
                }
            }
            $temp_instr = get_instructions($trans, $current_location, $nav_area);
            foreach($temp_instr as $inst){
                array_push($instruction_set, $inst);
            }
            array_push($instruction_set, 'Walk through door.');
            $orientation = $trans->trans_orient;

            $current_location = $trans->transition->trans_coord;
            $current_room = $temp_desired;
        }
        $nav_area = $current_room->nav_area;
        $temp_instr = same_room_instruction($current_location, $desired_location, $nav_area);
        foreach($temp_instr as $inst){
            array_push($instruction_set, $inst);
        }
    }
    return($instruction_set);
}

function get_node($number){
    global $nodes;
    foreach($nodes as $node){
        if ($node->no == $number) {
            return $node;
        }
    }
}

function generate_area($i_start, $i_lim, $j_start, $j_lim, $nav_area){
    for($i = $i_start; $i <= $i_lim; $i++){
        for($j = $j_start; $j < $j_lim; $j++){
            array_push($nav_area, array($i, $j));
        }
    }
    return $nav_area;
}

function generate_triangle_lower($i_start, $i_lim, $j_start, $j_lim, $nav_area){
    for($i = $i_start; $i < $i_lim; $i++){
        for($j = $j_start; $j < $j_lim; $j++){
            if($j <= (((-54/44)*$i)-314)){
                array_push($nav_area, array($i, $j));
            }
        }
    }
    return $nav_area;
}

//creating the space

//node
$node1 = new Coord(1, 0, 0);
$node2 = new Coord(2, 107, 0);
$node3 = new Coord(3, 240, 175);
$node4 = new Coord(4, 320, 638);
$node5 = new Coord(5, 240, 563);
$node6 = new Coord(6, 314, 458); 
$node7 = new Coord(7, 75, 750); 
$node8 = new Coord(8, 100, 150);
$node9 = new Coord(9, 380, 220);
$node10 = new Coord(10, 274, 323);
$node11 = new Coord(11, 425, 713);
$node12 = new Coord(12, 315, 718);
$nodes = array($node1, $node2, $node3, $node4, $node5, $node6, $node7, $node8, $node9, $node10, $node11, $node12);

//doors
$door1 = new Transition(1, $node2);
$door2 = new Transition(2, $node3);
$door3 = new Transition(3, $node4);
$door4 = new Transition(4, $node5);
$door5 = new Transition(5, $node6);

//effective shape
$hall_area = array();
$living_area = array();
$landing_area = array();
$kitchen_area = array();

//hall
$hall_area = generate_area(0, 197, 0, 112, $hall_area);
$hall_area = generate_area(100, 240, 112, 203, $hall_area);
$hall_area = generate_area(0, 240, 203, 268, $hall_area);
$hall_area = generate_area(120, 240, 268, 680, $hall_area);

//living
$living_area = generate_area(240, 530, 130, 220, $living_area);
$living_area = generate_area(274, 380, 220, 358, $living_area);
$living_area = generate_area(274, 354, 358, 458, $living_area);

//landing
$landing_area = generate_area(240, 420, 458, 567, $landing_area);
$landing_area = generate_area(240, 345, 567, 638, $landing_area);

//kitchen
$kitchen_area = generate_area(130, 570, 638, 718, $kitchen_area);
$kitchen_area = generate_area(315, 425, 718, 1018, $kitchen_area);
$kitchen_area = generate_area(425, 570, 813, 878, $kitchen_area);
$kitchen_area = generate_area(500, 570, 672, 878, $kitchen_area);
$kitchen_area = generate_area(0, 130, 688, 753, $kitchen_area);

//adjacencies
//hall to living room
$hall_living = new Adjacency(1, 1, 2, $door2, 'E');

//living room to hall
$living_hall = new Adjacency(2, 2, 1, $door2, 'W');

//living room to landing
$living_landing = new Adjacency(3, 2, 3, $door5, 'N');

//landing to living room
$landing_living = new Adjacency(4, 3, 2, $door5, 'S');

//hall to landing
$hall_landing = new Adjacency(5, 1, 3, $door4, 'E');

//landing to hall
$landing_hall = new Adjacency(6, 3, 1, $door4, 'W');

//landing to kitchen
$landing_kitchen = new Adjacency(7, 3, 4, $door3, 'N');

//kitchen to landing
$kitchen_landing = new Adjacency(8, 4, 3, $door3, 'S');

//rooms
//hall
$hall_nodes = array($node2, $node8);
$hall_adj = array($hall_living, $hall_landing);
$hall_doors = array($door2, $door4);
$bed = new Room(1, 'hall', $hall_nodes, $hall_adj, $hall_doors, $hall_area);

//living
$living_nodes = array($node9, $node10);
$living_adj = array($living_hall, $living_landing);
$living_doors = array($door2, $door5);
$living = new Room(2, 'living', $living_nodes , $living_adj, $living_doors, $living_area);

//landing
$landing_nodes = array();
$landing_adj = array($landing_kitchen, $landing_hall, $landing_living);
$landing_doors = array($door3, $door4, $door5);
$landing = new Room(3, 'landing', $landing_nodes, $landing_adj, $landing_doors, $landing_area);

//kitchen
$kitchen_nodes = array($node7, $node11, $node12);
$kitchen_adj = array($kitchen_landing);
$kitchen = new Room(4, 'kitchen', $kitchen_nodes, $kitchen_adj, $door3, $kitchen_area);

//rooms array
$rooms = array($bed, $living, $landing, $kitchen); 

//setting variables

//orientation, desired and current location
$current_location = $node2;
$orientation = 'N';
$desired_location = $node12;


//directed graph
$graph = array(
    1 => array(2, 3),
    2 => array(1, 3),
    3 => array(1, 2, 4),
    4 => array(3)
);

$g = new Graph($graph);

//execution

$desired_location = get_node($post_node);
$instructions = get_path($current_location, $desired_location, $g);
echo json_encode($instructions); 
/*
$instructions = get_path($current_location, $desired_location, $g);
var_dump($instructions); */
?>