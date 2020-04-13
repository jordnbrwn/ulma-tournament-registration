<?php

class Registrant {
  
    var $formID;
    var $formKeys;
    var $registrations;
    
    function __construct($formID) {
      $this->formID = $formID;
      
      global $wpdb;
      $keys = $wpdb->get_results('SELECT * FROM wp_nf3_fields WHERE parent_id = '.$formID);
      
      $labels = array();
          
      foreach($keys as $key) {
        $labels['_field_'.$key->id] = $key->label;
      }
      //print_r($labels);
      $this->formKeys = $labels;
      $this->registrations = array();
      
    }
    
    function get_registrations() {
      global $wpdb;
      
      // Get IDs of fields
      $IDs = $wpdb->get_results('SELECT wp_posts.ID FROM wp_posts JOIN wp_postmeta ON wp_postmeta.post_id = wp_posts.ID WHERE wp_posts.post_type = "nf_sub" AND wp_postmeta.meta_key = "_form_id" AND wp_postmeta.meta_value = "'.$this->formID.'"');
      
      foreach ($IDs as $id) {
        $id = $id->ID;
  
        $regs = $wpdb->get_results('SELECT * FROM `wp_postmeta` WHERE wp_postmeta.post_id = '.$id);
        
        print_r($regs);
        
        $events = unserialize($regs[8]->meta_value);
  
        $numberOfEvents = count($events);
        
        $tempEvent = "";
        
        array_push($this->registrations, $regs);
        
      }
  
      print_r($this->$registrations);
      
      $registrations = $this->clean_up_data($this->registrations);
      $prevID = 0;
      $counter = 0;
      $doubled = 0;
      $individuals = array();
      
      
      for ($x = 0; $x < count($registrations); $x++) {
        $reg = $registrations[$x];
        
        $tempArray = array();
        
        for($y = 0; $y < count($reg); $y++) {
          
          if ($reg[$y][0] == "") continue;
          
          //Individual Desc
          $desc = $reg[$y][0];
          //Individual Detail
          $value = $reg[$y][1];
          
          $tempArray[$desc] = $value;
          
        }
        
        $individuals[$x] = $tempArray;
        
      }
      
      //print_r($individuals);
      
      $individuals = $this->separate_into_divisions($individuals);
      
      return $individuals;
    }
    
    function separate_into_divisions($individuals) {
      
      $newIndividualsArray = array();
      
      for($x = 0; $x < count($individuals); $x++) {
        $individual = $individuals[$x];
        $tempIndividual = $individual;
        
        $events = unserialize($individual["Tournament Events"]);
        $numEvents = count($events);
        
        if ($numEvents == 1) {
  
          $individuals[$x]["Tournament Events"] = $events[0];
          $individual["Tournament Events"] = $events[0];
          
          array_push($newIndividualsArray, $individual);
          
          continue;
        }
        
        //Loop through the events and create duplicate invidiuals and change the event type to match the entry      
        for ($y = 0; $y < count($events); $y++) {
          
          $tempIndividual["Tournament Events"] = $events[$y];
          
          $remove_character = array("\n", "\r\n", "\r");
          $tempIndividual["Team Member Names"] = str_replace($remove_character, " ", $tempIndividual["Team Member Names"]);
          
          if ($y == 0) {
            
            $individuals[$x] = $tempIndividual;  
            
            array_push($newIndividualsArray, $tempIndividual);
            
          }
          else {
            
            array_push($newIndividualsArray, $tempIndividual);
            
          }
          
        }
        
      } 
      
      return $newIndividualsArray;  
    }
    
    function separate_into_csvs($registrations) {
      
      foreach($registrations as $registration) {
        print_r($registration);
        switch($registration["Tournament Events"]) {
          case "forms-sparring":
            if ($registration["Program"] == "lil-leader") {
              $this->print_registration_to_csv($registration, "lil-leaders.csv", 29);
            } else {
              switch($registration["Rank"]) {
                case "first":
                  if ($registration["Age"] >= 5 && $registration["Age"] <= 9) {
                    $this->print_registration_to_csv($registration, "forms-sparring.csv", 16);
                  } else if ($registration["Age"] >= 10 && $registration["Age"] <= 13) {
                    $this->print_registration_to_csv($registration, "forms-sparring.csv", 15);
                  } else {
                    if ($registration["Gender"] == "male") {
                      $this->print_registration_to_csv($registration, "forms-sparring.csv", 5);
                    } else {
                      $this->print_registration_to_csv($registration, "forms-sparring.csv", 10);
                    }
                  }
                break;
                case "second":
                  if ($registration["Age"] >= 5 && $registration["Age"] <= 9) {
                    $this->print_registration_to_csv($registration, "forms-sparring.csv", 14);
                  } else if ($registration["Age"] >= 10 && $registration["Age"] <= 13) {
                    $this->print_registration_to_csv($registration, "forms-sparring.csv", 13);
                  } else {
                    if ($registration["Gender"] == "male") {
                      $this->print_registration_to_csv($registration, "forms-sparring.csv", 4);
                    } else {
                      $this->print_registration_to_csv($registration, "forms-sparring.csv", 9);
                    }
                  }
                break;
                case "third":
                  if ($registration["Age"] >= 5 && $registration["Age"] <= 9) {
                    $this->print_registration_to_csv($registration, "forms-sparring.csv", 12);
                  } else if ($registration["Age"] >= 10 && $registration["Age"] <= 13) {
                    $this->print_registration_to_csv($registration, "forms-sparring.csv", 11);
                  } else {
                    if ($registration["Gender"] == "male") {
                      $this->print_registration_to_csv($registration, "forms-sparring.csv", 3);
                    } else {
                      $this->print_registration_to_csv($registration, "forms-sparring.csv", 8);
                    }
                  }
                break;
                case "fourth":
                  if ($registration["Gender"] == "male") {
                    $this->print_registration_to_csv($registration, "forms-sparring.csv", 2);
                  } else {
                    $this->print_registration_to_csv($registration, "forms-sparring.csv", 7);
                  }
                break;
                case "fifth":
                  if ($registration["Gender"] == "male") {
                    $this->print_registration_to_csv($registration, "forms-sparring.csv", 1);
                  } else {
                    $this->print_registration_to_csv($registration, "forms-sparring.csv", 6);
                  }
                break;
                case "brown":
                case "sr-brown":
                case "red":
                case "sr-red":
                  if ($registration["Age"] >= 5 && $registration["Age"] <= 9) {
                    $this->print_registration_to_csv($registration, "forms-sparring.csv", 26);
                  } else if ($registration["Age"] >= 10 && $registration["Age"] <= 13) {
                    $this->print_registration_to_csv($registration, "forms-sparring.csv", 25);
                  } else {
                    if ($registration["Gender"] == "male") {
                      $this->print_registration_to_csv($registration, "forms-sparring.csv", 27);
                    } else {
                      $this->print_registration_to_csv($registration, "forms-sparring.csv", 28);
                    }
                  }
                break;
                case "purple":
                case "sr-purple":
                case "blue":
                case "sr-blue":
                case "green":
                case "sr-green":
                case "orange":
                case "sr-orange":
                  if ($registration["Age"] >= 5 && $registration["Age"] <= 9) {
                    $this->print_registration_to_csv($registration, "forms-sparring.csv", 22);
                  } else if ($registration["Age"] >= 10 && $registration["Age"] <= 13) {
                    $this->print_registration_to_csv($registration, "forms-sparring.csv", 21);
                  } else {
                    if ($registration["Gender"] == "male") {
                      $this->print_registration_to_csv($registration, "forms-sparring.csv", 23);
                    } else {
                      $this->print_registration_to_csv($registration, "forms-sparring.csv", 24);
                    }
                  }
                break;
                case "yellow":
                case "white":
                  if ($registration["Age"] >= 5 && $registration["Age"] <= 9) {
                    $this->print_registration_to_csv($registration, "forms-sparring.csv", 18);
                  } else if ($registration["Age"] >= 10 && $registration["Age"] <= 13) {
                    $this->print_registration_to_csv($registration, "forms-sparring.csv", 17);
                  } else {
                    if ($registration["Gender"] == "male") {
                      $this->print_registration_to_csv($registration, "forms-sparring.csv", 19);
                    } else {
                      $this->print_registration_to_csv($registration, "forms-sparring.csv", 20);
                    }
                  }
                break;
              }
            }
          
          break;
          case "boards":
            $this->print_registration_to_csv($registration, "boards.csv", 49);
          break;
          case "team-forms":
            $this->print_registration_to_csv($registration, "team-forms.csv", 50);
          break;
          case "weapons":
            switch($registration["Rank"]) {
              case "first":
              case "second":
              case "third":
              case "fourth":
              case "fifth":
                if ($registration["Age"] > 13) {
                  if ($registration["Gender"] == "male") {
                    $this->print_registration_to_csv($registration, "weapons.csv", 40);
                  } else {
                    $this->print_registration_to_csv($registration, "weapons.csv", 41);
                  }
                }
                else {
                  $this->print_registration_to_csv($registration, "weapons.csv", 42);
                }
              break;
              case "brown":
              case "sr-brown":
              case "red":
              case "sr-red":
                if ($registration["Age"] > 13) {
                  if ($registration["Gender"] == "male") {
                    $this->print_registration_to_csv($registration, "weapons.csv", 43);
                  } else {
                    $this->print_registration_to_csv($registration, "weapons.csv", 44);
                  }
                }
                else {
                  print_registration_to_csv($registration, "weapons.csv", 45);
                }
              break;
              case "purple":
              case "sr-purple":
              case "blue":
              case "sr-blue":
              case "green":
              case "sr-green":
              case "orange":
              case "sr-orange":
                if ($registration["Age"] > 13) {
                  if ($registration["Gender"] == "male") {
                    $this->print_registration_to_csv($registration, "weapons.csv", 46);
                  } else {
                    $this->print_registration_to_csv($registration, "weapons.csv", 47);
                  }
                }
                else {
                  $this->print_registration_to_csv($registration, "weapons.csv", 48);
                }
              break;
            }
          break;
          case "musical":
            switch($registration["Rank"]) {
              case "first":
              case "second":
              case "third":
              case "fourth":
              case "fifth":
                if ($registration["Age"] > 13) {
                  if ($registration["Gender"] == "male") {
                    $this->print_registration_to_csv($registration, "musical.csv", 31);
                  } else {
                    $this->print_registration_to_csv($registration, "musical.csv", 32);
                  }
                }
                else {
                  $this->print_registration_to_csv($registration, "musical.csv", 33);
                }
              break;
              case "brown":
              case "sr-brown":
              case "red":
              case "sr-red":
                if ($registration["Age"] > 13) {
                  if ($registration["Gender"] == "male") {
                    $this->print_registration_to_csv($registration, "musical.csv", 34);
                  } else {
                    $this->print_registration_to_csv($registration, "musical.csv", 35);
                  }
                }
                else {
                  $this->print_registration_to_csv($registration, "musical.csv", 36);
                }
              break;
              case "purple":
              case "sr-purple":
              case "blue":
              case "sr-blue":
              case "green":
              case "sr-green":
              case "orange":
              case "sr-orange":
                if ($registration["Age"] > 13) {
                  if ($registration["Gender"] == "male") {
                    $this->print_registration_to_csv($registration, "musical.csv", 37);
                  } else {
                    $this->print_registration_to_csv($registration, "musical.csv", 38);
                  }
                }
                else {
                  $this->print_registration_to_csv($registration, "musical.csv", 39);
                }
              break;
            }
          break;
          
          
          
        }  
      }
      
    }
    
    function print_registration_to_csv($registrations, $fileName, $category) {
      $file = fopen($fileName,"a");
      
      if (false === $file) {
          throw new RuntimeException('Unable to open log file for writing');
      }
      
      $registrantCount = 0;
      $x = 1;
      $csv = "";
      $y = 0;
  
      foreach ($registrations as $key => $registrant) {
        //print count($registrant);
        
        $csv .= (string)$registrant;
        
        if ($x < count($registrations)) {
          $csv .= ", ";
        }
        
      }
  
      $csv .= $category;
      $csv .= "\n";
      
      //print $csv;
      
      $bytes = fwrite($file, $csv);
      
      printf('Wrote %d bytes to %s', $bytes, realpath($fileName));
      
      fclose($file);
    }
  
  
  
  
  function print_all_registrations_to_csv($registrations, $fileName, $category) {
      $file = fopen($fileName,"a");
      
      if (false === $file) {
          throw new RuntimeException('Unable to open log file for writing');
      }
      
      $registrantCount = 0;
      $x = 0;
      $csv = "";
      $y = 0;
      
      foreach ($registrations as $key => $registrant) {
        //print count($registrant);
        $x = 0;
        
        print_r($registrant);
        
        if ($y == 0) {
          foreach ($registrant as $key => $value) {
            $csv .= (string)$key;
            if ($x < 10) {
              $csv .= ", ";
            }
            $x++;
          }
          $csv .= ", category";
          $csv .= "\n";
        }
        $x = 0;
        $y = 1;
        foreach ($registrant as $detail) {
          $csv .= (string)$detail;
          if ($x < 12) {
            $csv .= ", ";
          }
          $x++;
        }
        $csv .= $category."\n";
        
      }
      
      $bytes = fwrite($file, $csv);
      
      printf('Wrote %d bytes to %s', $bytes, realpath($fileName));
      
      fclose($file);
    }
  
  
  
  
    
    function the_registrations() {
      
      $registrations = $this->get_registrations();
      
      //print_r($registrations);
      
      print '<table id="registrations"><thead><tr><td>Student Name</td><td>Age</td><td>Instructor</td><td>Age Group</td><td>Rank</td><td>Lil Leader Rank</td><td>City</td><td>State</td><td>Gender</td><td>Event</td><td>Team Info</td></tr></thead><tbody>';
      
      foreach($registrations as $student) {
        print '<tr>';
        foreach($student as $field) {
          if (!isset($field[0])) continue;
          if ($field[0] == "Tournament Events") {
            $field[1] = implode(", ",unserialize($field[1]));
          }
          print '<td>'.$field[1].'</td>';
        }
        print '</tr>';
      }
      
      print '</tbody></table>';
      
    }
    
    function clean_up_data($regs) {
      
      $registrations = array();
      $tempRegs = array();
      
      //print_r($regs);
      
      foreach($regs as $reg) {
        $tempRegs = array();
        foreach($reg as $field) {
          $regValues = array();
          array_push($regValues, $this->formKeys[$field->meta_key]);
          array_push($regValues, $field->meta_value);
          array_push($regValues, $field->post_id);
          array_push($tempRegs, $regValues);
        }
        array_push($registrations, $tempRegs);
      }
      return $registrations;
    }
    
  }