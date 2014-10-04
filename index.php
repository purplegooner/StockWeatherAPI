<?php
  header('conternt-Type: application/json');
  //stores search params
  $stock = $_GET["stock"];
  $location = $_GET["location"];
  $locationStatus;

  $stock_symbol = getStockData($stock);
  searchLocation($location);

  function getStockData($company){
    $url = "http://d.yimg.com/autoc.finance.yahoo.com/autoc?query=".$company."&callback=YAHOO.Finance.SymbolSuggest.ssCallback";
    $rawJson = fileGetContents($url);
    $fixedJson = explode("(",$rawJson);
    $fixedJson = explode(")", $fixedJson[1]);
    $fixedJson = $fixedJson[0];
    $JSON = json_decode($fixedJson);

    $symbol = $JSON->ResultSet->Result[0]->symbol;

    return $symbol;
  }


  /*
  * Searches Locations and checks if there are
  * multiple found, weather data retrieved, or an error
  * @param {string} $_location - locationSearchQuery
  */
  function searchLocation($_location){
    global $locationStatus;
    global $currentWeather;
    global $results;
    $url = "http://api.wunderground.com/api/d8d8c5e34649bbd5/conditions/q/".$_location.".json";
    $rawJson = fileGetContents($url);
    $JSON = json_decode($rawJson);
    $errors = $JSON->response->error;
    $results = $JSON->response->results;
    if (count($errors)>0){
      //echo("There Was An Error");
      $locationStatus = "error";
    }elseif(count($results)>0){
      //echo("multipleLocationsFound");
      $locationStatus = "multiple";
    }else{
      //echo("Weather Data Was Found");
      $locationStatus = "data";
      $currentWeather = $JSON->current_observation;
    }
  }

  function getWeatherData($location){

  }

  /*
  *Rename file_get_contents to fileGetContents
  * @param {string} $file - @see file_get_contents
  */
  function fileGetContents($file){
    $returned = file_get_contents($file);
    return $returned;
  }
?>
{
  "status" : "<?php echo($locationStatus); ?>",
  "symbol" : "<?php echo($stock_symbol); ?>"<?php if($locationStatus != "error"){ echo(","); }

  if($locationStatus == "data"){ ?>
    "city" : "<?php echo($currentWeather->display_location->city); ?>"
  <?php }else if($locationStatus == "multiple"){ ?>
  <?php } ?>
}
