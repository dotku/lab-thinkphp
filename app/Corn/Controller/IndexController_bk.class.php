<?php
namespace Corn\Controller;
use Think\Controller;
class IndexController extends Controller {

    public function _initialize() {
        $this->city = 'San Francisco';
        if($_GET['city']){
            $this->city = $_GET['city'];
        }
    }

    public function indexOp() {
        $this->display('common/head');
        $this->mediaOp();
        // $this->statOp();
        // $this->georeset($_GET);
        $this->display('common/footer');
    }

    /**
    * [PLAN] stat should be a individual module,
    * but right now, I just keep it simple and combine with corn module
    */
    
    public function statOp() {
        $model_stat = D('stat');
        // list first 100 city stat
        $output['data']['stat'] = $model_stat->limit(100)->select();
        $this->output = $output;
        //var_dump($output['data']['stat']);
        $this->display('stat');
    }

    public function georesetAllOp() {
        $this->description = 'used to clean up all wrong geo settings';
        $this->display('common/head');
        $this->georesetAll();
        //$this->display('common/general_corn');
        $this->display('common/footer');
    }
    
    public function mediaresetAllOp() {
        $model_stat = D('stat');
        $list_stat = $model_stat->select();
        foreach ($list_stat as $key => $val) {
            $media_total = $val['estimatedresultcount_video']
                + $val['estimatedresultcount_news'];
            if ($val['media_total'] != $media_total) {
                var_dump($val);
                $this->mediareset($val);
            } else {
                var_dump('id=' . $val['id'] . ' is vaild');
            }
        }
    }
    
    public function mediareset($map) {
        $model_stat = D('stat');
        $info_stat = $model_stat->where($map)->find();
        var_dump($info_stat);
        $info_stat['media_total'] = 
            $info_stat['estimatedresultcount_video']
            + $info_stat['estimatedresultcount_news'];
        $model_stat->where('id=' . $map['id'])->save($info_stat);
    }
    
    /*
    * 1. clean all latLgn = 0
    * 2. 对数组要审核处理
    * 3. 应该是全面的清理动作，目前只是整理最新的 1000 个
    */
    
    public function georesetAll() {
        $results = array();
        $model_stat = D('stat');
        // $map_stat['lat'] = array('eq', '0.0000000');
        // $map_stat['lng'] = array('eq', '0.0000000');
        // $map_stat['latLng'] = 0;

        // $map_stat['city'] = 'san jose';
        $result['count']['total'] = intval($model_stat->count());
        $result['count']['select'] = count($model_stat->where($map_stat)->select());
        $list_stat = $model_stat->select();
        // var_dump('result', $result);
        //var_dump($map_stat);

        //$this->georeset($list_stat[0]);
        var_dump('list_stat', $list_stat);
        
        foreach($list_stat as $key =>$val) {
            $latLng = unserialize($val['latlng']);
            //$latLng['lat'] = strval($latLng['lat']);
            //$latLng['lng'] = strval($latLng['lng']);
            // var_dump('unserialize', $latLng);
            // var_dump($latLng['lat'] ===$val['lat']);
            if (!is_array($latLng) || 
                $latLng['lat'] !== $val['lat'] || 
                $latLng['lng'] != $val['lng']) {
                // var_dump('$val', $val);
                $this->georeset($val);
            }
            
        }
        
        // var_dump($rows_stat);
    }

    public function georesetOp() {
        $this->display('common/head');
        $this->georeset($_GET);
        $this->display('Index/georeset');
        $this->display('common/footer');
    }
    
    /**
    * @input $fields
    * @output $result
    */
    
    public function georeset($map_stat){
        $model_stat = D('stat');
        $model_geolite2_cn = D('geolite2_cn');
        $mapquestURL = 'http://www.mapquestapi.com/geocoding/v1/address?key=';
        $qqmapURL = 'http://apis.map.qq.com/ws/geocoder/v1/?key=ZR4BZ-I47WJ-PRJFX-FRECB-6M5JV-HPFHI&address=';
        $requestURL = '';
        // var_dump('map_stat', $map_stat);
        $info_stat = $model_stat->where($map_stat)->find();
        $map_geolite2_cn['city_name'] = $info_stat['city'];
        $info_geolite2_cn = $model_geolite2_cn->where($map_geolite2_cn)->find();
        if (!unserialize($info_stat['latLng'])) {
            if ($info_geolite2_cn) {
                $requestURL = $qqmapURL.$map_stat['city'];
            } else if ($info_stat['country']) {
            $requestURL = $mapquestURL . C('MAPQUEST_KEY') . 
                '&location=' . urlencode($info_stat['city']) . ',' . $info_stat['country'];
                $this->georesetByMapquest($requestURL, $map_stat);
            } else {
                $requestURL = $mapquestURL . C('MAPQUEST_KEY') . 
                '&location=' . urlencode($info_stat['city']);
                $this->georesetByMapquest($requestURL, $map_stat);
            }

        }
        
        /** 
        var_dump('latLng', unserialize($info_stat['latLng']));
        var_dump('url', $requestURL);
        var_dump($info_stat);
        var_dump($rsp['results'][0]['locations'][0]['latLng']);
        */
    }

    public function georesetByQQ($requestURL, $map_stat) {
        $model_stat = D('stat');
        $info_stat = $model_stat->where($map_stat)->find();
        $rsp = json_decode(file_get_contents($requestURL), true);
        $latLng['lng'] = strval($rsp['result']['location']['lng']);
        $latLng['lat'] = strval($rsp['result']['location']['lat']);
        $info_stat['country'] = 'cn';
        $info_stat['lat'] = strval($latLng['lat'] );
        $info_stat['lng'] = strval($latLng['lng']);
        $info_stat['latLng'] = serialize($latLng);
        $model_stat->save($info_stat);
    }

    public function georesetByMapquest($requestURL, $map_stat) {
        $model_stat = D('stat');
        $info_stat = $model_stat->where($map_stat)->find();
        $rsp = json_decode(file_get_contents($requestURL), true);
        $latLng = $rsp['results'][0]['locations'][0]['latLng'];
        //var_dump($rsp['results'][0]);
        //var_dump('latLng', $latLng);
        $latLng['lat'] = strval($latLng['lat']);
        $latLng['lng'] = strval($latLng['lng']);
        $info_stat['country'] = $rsp['results'][0]['locations'][0]['adminArea1'];
        $info_stat['lat'] = strval($rsp['results'][0]['locations'][0]['latLng']['lat']);
        $info_stat['lng'] = strval($rsp['results'][0]['locations'][0]['latLng']['lng']);
        $info_stat['latLng'] = serialize($latLng);
        //var_dump('latLng', $latLng);
        var_dump('info_stat', $info_stat);
        $model_stat->save($info_stat);
    }
    
    /**
    * mediaOp will run both videoOp and newsOp.
    * for better performance, videoOp and newsOp should not on the 
    * same cron list with mediaOp.
    */
    
    public function mediaOp() {
        $model_stat = D('stat');
        $this->videoOp();
        $this->newsOp();
        $map_stat['city'] = $this->city;
        $info_stat = $model_stat->where($map_stat)->find();
        $info_stat['media_total'] = intval($info_stat['estimatedresultcount_video']) + intval($info_stat['estimatedresultcount_news']);
        // var_dump($info_stat);
        $model_stat->where($map_stat)->save($info_stat);
    }

    public function newsOp() {
        // retrieve 64 videos for maxium
        for($i = 0; $i < 8; $i++) {
           $this->news_google($i*8, $this->city);
        }
        // $this->news_google(0, $this->city);
    }
    /*
    public function videoOp() {
        var_dump("抓取视频信息中...");
        // retrieve 64 videos for maxium
        for($i = 0; $i < 8; $i++) {
            var_dump("start = " . $i*8);
            $this->video_google($i*8, $this->city);
        }
        // $this->video_google(0, $this->city);
    }
    */
    /**
    * required: 
    *   q, query keyword
    *   v, version
    * optional: 
    *   callback, for JavaScript only
    *   content, work with callback, for JavaScript only
    *   hl, host language
    */
    
    public function video_googleOp() {
        var_dump('Welcome to video corn job!');
        var_dump('Initialized the variables.');
        $input = array();
        $output = array();

        $required['v']      = '1.0';
        $required['q']      = $_GET['city'];

        $options['start']   = 8 * ($_GET['index'] ? $_GET['index'] : 0);
        $options['rsz']     = 8;
        $options['userip']  = $_SERVER['REMOTE_ADDR'];
        $options['scoring'] = 'd';

        $secret['apiURL'] = 'https://ajax.googleapis.com/ajax/services/search/video';
        
        $input['required']  = $required;
        $input['options']   = $options;

        var_dump('Check required fields.');
        foreach ($required as $key => $val) {
            if (!$val) {
                $output['message'] = 'Required field city is missing.';
                var_dump('input', $input);
                var_dump('output', $output);
                return -1;
            }
        }

        var_dump('Required fields verified.');
        var_dump('[Display optional fields]');
        var_dump($options);
        var_dump('Setup API request keys');

        foreach ($options as $key => $val) {
            $options_url .= "&$key=". rawurlencode($val);
        }
        
        foreach ($required as $key => $val) {
            $required_url .= "&$key=". rawurlencode($val);
        }

        var_dump('[Display request keys]');
        
        var_dump($options_url, $required_url);
        
        var_dump('Create the requestURL');
        
        $requestURL = 'https://ajax.googleapis.com/ajax/services/search/video?' 
            . $required_url . $options_url;
        
        var_dump('Create modules');

        //$model_video = D('media_video');
        $model_stat = D('stat');
        $data_stat['sample'] = 'value';
        $model_stat->save();

        var_dump('Request video data');
        $data_respond = json_decode(file_get_contents($requestURL), true);
        var_dump($data_respond);

        var_dump('Create stat container');
        
        $map_stat['city'] = $_GET['city'];
        $info_stat = $model_stat->where($map_stat)->find();
        $data_stat = array();

        var_dump('Normalize the respond data');
        foreach($data_respond['responseData']['cursor'] as $key => $val) {
            $data_respond['responseData']['cursor'][strtolower($key)] = $val;

            if(strcmp($key, strtolower($key)) != 0){
                unset($data_respond['responseData']['cursor'][$key]);
            }

        }
        var_dump($data_respond['responseData']['cursor']);
        
        if ($info_stat){
            $map_stat['id'] = $info_stat['id'];
            var_dump('City found');
            var_dump($info_stat);
            var_dump('Compare and update stat');
            $respond_count = intval($data_respond['responseData']['cursor']['estimatedresultcount']);
            $database_count = intval($info_stat['estimatedresultcount_video']);
            var_dump('$respond_count', $respond_count);
            //if($respond_count > $database_count) {
                $data_stat['estimatedResultCount_video'] = 12345;
                $data_stat['id'] = $info_stat['id'];
                var_dump($data_stat);
                $model_stat->save($data_stat);
                //var_dump($model_stat->where('id='.$data_stat['id'])->find());
            //}
        } else {
            var_dump('City not found');
            var_dump('Create new stat record');
            $info_stat['estimatedResultCount_video'] 
                = $data_respond['cursor']['estimatedResultCount'];
            $info_stat['city'] = $_GET['city'];
            if ($model_stat->add($info_stat)){
                var_dump('New stat record created successfully');
            }
        }
        
        return 1;

        
        foreach ($options as $key => $val) {
            $options_url .= "&$key=". rawurlencode($val);
        }
        
        foreach ($required as $key => $val) {
            $required_url .= "&$key=". rawurlencode($val);
        }
        
        //var_dump($options_url);
        //var_dump($required_url);
       
        //$url_video = 'https://ajax.googleapis.com/ajax/services/search/video?v=1.0&rsz=8&q=' . $keywords;
        
        //$url_video = 'http://www.google.com/advanced_video_search?' . $required_url . $options_url;
        
        //$url_news = 'https://ajax.googleapis.com/ajax/services/search/news?v=1.0' .'&hl='.$option['hl'] .'&q=' . $keywords;
        //var_dump($url_video);
        
        //$data_news = json_decode(file_get_contents($url_news), true);
        
        // save to database
        $data['input'] = $data_video['responseData'];
        
        
        foreach ($data['input']['results'] as $key => $val) {
            $input_video = $val;
            
            // use key to determinate saving
            // eg. https://www.youtube.com/watch?v=8RZqPq1-1Tw
            // the key would be '8RZqPq1-1Tw'
            $input_video['key'] = $map_video['key'] = explode('v=', $val['url'])[1];
            $input_video['embed'] = 'https://www.youtube.com/embed/' . $input_video['key'];
            $input_video['published'] = date ("Y-m-d H:i:s", strtotime($input_video['published']));
            $input_video['updated_date'] = date ("Y-m-d H:i:s", time());
            //var_dump($input_video['updated_date']);
            $info_video = $model_video->where($map_video)->find();
            //var_dump($info_video);
            
            if (!$info_video) {
                $model_video->add($input_video);
            } else {
                $model_video->where($map_video)->save($input_video);
            }
            $info_video = $model_video->where($map_video)->find();
            //var_dump($info_video);
        }
        
        // var_dump( $data_video['responseData']['results'] );
        // working with stat
        // [PLAN] It should save to log system later
        // should not share with content database
        $input_stat['estimatedResultCount_video'] = intval($data_video['responseData']['cursor']['estimatedResultCount']);
        $input_stat['city'] = $map_stat['city'] = $city;
        $input_stat['updated_date'] = date ("Y-m-d H:i:s", time());
        
        $info_stat = $model_stat->where($map_stat)->find();
        //$model_stat->add($input_stat);
        //var_dump($data_video);
        //var_dump($input_stat);
        //var_dump($info_stat);
        
        if ($info_stat) {
            //var_dump('saving');
            $model_stat->where($map_stat)->save($input_stat);
        } else {
            $model_stat->add($input_stat);
        }
        //*/
        //var_dump();
        // var_dump($data['input']);
    }

    public function news_google($start=0, $city='San Francisco') {
        $required = array();
        $options = array();
        $required_url = '';
        $options_url = '';
        $model_news = D('media_news');
        $model_stat = D('stat');
        
        $required['v'] = '1.0';
        $required['q'] = $city;
        $options['rsz'] = 8;
        $options['userip'] = $_SERVER['REMOTE_ADDR'];
        $options['scoring'] = 'd';
        $options['start'] = $start;
        // $options['tbs'] = 'qdr:d'; 无效的以时间作为方式的过滤器
        
        if (isset($_GET['keywords'])) {
            $required['q'] = rawurlencode($_GET['keywords']);
        }
        
        $options = array_merge($options, $_GET);
        unset($options['keywords']);
        
        foreach ($options as $key => $val) {
            $options_url .= "&$key=". rawurlencode($val);
        }
        
        foreach ($required as $key => $val) {
            $required_url .= "&$key=". rawurlencode($val);
        }
        
        $url_news = 'https://ajax.googleapis.com/ajax/services/search/news?' . $required_url . $options_url;
        //var_dump($url_news);
        //$url_news = 'http://www.google.com/advanced_news_search?' . $required_url . $options_url;
        
        //$url_news = 'https://ajax.googleapis.com/ajax/services/search/news?v=1.0' .'&hl='.$option['hl'] .'&q=' . $keywords;

        $data_news = json_decode(file_get_contents($url_news), true);
        //var_dump($data_news['responseData']['results']);
        
        // save to database
        $data['input'] = $data_news['responseData'];
        foreach ($data['input']['results'] as $key => $val) {
            $input_news = $val;

            // use key to determinate saving
            // eg. md('http://www.sfchronicle.com/...') = cc1fd14c78527e83d81b
            // the key would be 'cc1fd14c78527e83d81b'

            $input_news['key'] = $map_news['key'] = md5($val['unescapedUrl']);
            $input_news['published'] = date ("Y-m-d H:i:s", strtotime($input_news['publishedDate']));
            if ($input_news['image']['tbUrl']) {
                $input_news['tbUrl'] = $input_news['image']['tbUrl'];
            }
            $input_news['updated_date'] = date ("Y-m-d H:i:s", time());
            //var_dump($input_news['updated_date']);
            $info_news = $model_news->where($map_news)->find();
            //var_dump($input_news);
            //var_dump($map_news);
            //var_dump($info_news);
            
            if (!$info_news) {
                $model_news->add($input_news);
            } else {
                $model_news->where($map_news)->save($input_news);
            }
            //*/
            $info_news = $model_news->where($map_news)->find();
            //var_dump($info_news);
        }
        
        // working with model_stat
        $input_stat['city'] = $city;
        //$input_stat['']
        $input_stat['estimatedResultCount_news'] = intval($data_news['responseData']['cursor']['estimatedResultCount']);
        $input_stat['city'] = $map_stat['city'] = $city;
        $input_stat['updated_date'] = date ("Y-m-d H:i:s", time());
        
        $info_stat = $model_stat->where($map_stat)->find();
        //$model_stat->add($input_stat);
        //var_dump($data_video);
        //var_dump($input_stat);
        //var_dump($info_stat);
        var_dump($input_stat['estimatedResultCount_news'] );
        if ($info_stat) {
            //var_dump('saving');
            $model_stat->where($map_stat)->save($input_stat);
        } else {
            $model_stat->add($input_stat);
        }
        
        //var_dump( $data_news['responseData']);
        // var_dump($data_news['responseData']);
        // var_dump($data['input']);
        //*/
    }
}