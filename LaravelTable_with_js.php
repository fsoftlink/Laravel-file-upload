class ActionController extends Controller
{
    public function rowEdit(Request $req)
    {
        $data = $req->all();
        $tblString = 'CustomerAddress';
        $modelClass = "App\\Models\\" . $tblString;
        if (class_exists($modelClass)) {
            // Dynamically call model
            //$rows = $modelClass::where('user_id', $user->email)->get();
            return response()->json(['status' => 'success', 'message' => "Model  found!", 'data'=> $data]);
        } else {
            return response()->json(['status' => 'error', 'message' => "Model {$modelClass} not found!"]);
        }
    }

    public function rowDelete(Request $req)
    {
        $data = $req->all();
        $tblString = 'CustomerAddress';
        $modelClass = "App\\Models\\" . $tblString;

        if (class_exists($modelClass)) {
            $model = app($modelClass); // Model ka object create hoga
            //$rows = $model->where('user_id', $user->email)->get();
            return response()->json(['status' => 'success', 'message' => "Model  found!", 'data'=> $data]);
        }else{
            return response()->json(['status' => 'error', 'message' => "Model {$modelClass} not found!"]);
        }
    }

    public function rowInput(Request $req){
        $data = $req->all();
        return response()->json(['status' => 'success', 'message' => "Model Input found!", 'data'=> $data]);
    }
    public function rowSelect(Request $req){
        $data = $req->all();
        return response()->json(['status' => 'success', 'message' => "Model Select  found!", 'data'=> $data]);
    }
    public function rowRadio(Request $req){
        $data = $req->all();
        return response()->json(['status' => 'success', 'message' => "Model Radio found!", 'data'=> $data]);
    }
    public function rowCheckbox(Request $req){
        $data = $req->all();
        return response()->json(['status' => 'success', 'message' => "Model Checkbox found!", 'data'=> $data]);
    }
}
class ProfileController extends Controller
{
	public function userShippingAddress()
    {
        $user = Auth::user();
        //$address = CustomerAddress::where('user_id', $user->email)->where('status', 'Active')->first() ?? new CustomerAddress();
        $address =  new CustomerAddress();
        
        $configTemp = [
            'sl' => ['SL', '', false,'c','bold'],
            'pk' => ['PK', '', false,'c','bold'],
            'first_name' => ['First Name', '', false,'l','bold'],
            'last_name' => ['Last Name', '', false,'l', false],
            // 'phone' => ['Phone', '', false,'l', false],
            // 'email' => ['Email', '',false,'l','normal'],
            // 'city' => ['City', '',false,'l','normal'],
            // 'state' => ['State', '',false,'l','normal'],
            // 'zip' => ['Zip', '',false,'l','normal'],
            'address' => ['address', '',false,'l','normal'],
            '_del' => ['Delete', '',false,'l','normal'],
            '_edit' => ['Edit', '',false,'l','normal'],
            '_input' => ['Input', '',false,'l','normal'],
            '_select' => ['Select', '',false,'l','normal'],
        ];
        $thead = smsColumnConfig($configTemp);

        $tbody = [];
        $rows = CustomerAddress::where('user_id',$user->email)->get();
        foreach ($rows as $key => $row) {
            //$del = json_encode(['id'=>$row->id,'tbl'=> 'CustomerAddress','action'=> route('front.updateShippingAddress'), 'callback'=>'myPostAsk']);
            $del = json_encode(['pk'=> 'id' ,'pkv'=>$row->id,'tbl'=> 'CustomerAddress','action'=> route('front.rowDelete')]);
            $edit = json_encode([
                'pk'=> 'id' ,'pkv'=>$row->id, 'name'=> 'first_name' ,'value'=> $row->first_name, 
                'tbl'=> 'CustomerAddress', 'action'=> route('front.rowSelect'),
            ]);
            $option = [
                ['label'=>'Active', 'value'=>'Active', 'selected'=>false],
                ['label'=>'Dective', 'value'=>'Dective', 'selected'=>true],
            ];
            $temp = [
                'sl' => $key+1,
                'pk' => $row->id,
                'first_name' => $row->first_name,
                'last_name' =>  $row->last_name ,
                'phone' => $row->phone,
                'email' => $row->email,
                'city' => $row->city,
                'state' => $row->state,
                'zip' => $row->zip,
                'address' => $row->address,
                '_del' => ['payload'=>$del, 'func'=>'myPostAsk'],
                '_edit' => ['payload'=>$edit, 'func'=>'myPostAsk'],
                '_input' => ['payload'=>$edit, 'func'=>'myPostAsk','type'=> 'text', 'value'=>$row->first_name ], 
                '_select' => ['payload'=>$edit,  'func'=>'myPostAsk','type'=> 'select', 'option'=>$option ], 
            ];
            $tbody[] = array_intersect_key($temp, array_flip(array_keys($thead)));   //break;
        }
        $role = 'admin';
        $return = [
            'role'=> $role,
            'right'=> in_array($role, ['admin','principal']) ? 'edit':'view',
            'skip_keys'=> in_array($role, ['admin2']) ? []:['pk'],
            'checkbox' => in_array($user->role, ['admin','principal']) ? false : false,
            'thead'=> $thead,
            'tbody'=> $tbody,
        ];

        return view('front.profile.address', compact('address', 'return'));
    }
}
function smsColumnConfig($config = [], $keyword = ''){
    $arr = [];
    $defaultValues = ['label' => '', 'filter' => '', 'edit' => false, 'align' => 'l', 'fw'=>'normal']; 
    foreach ($config as $k => $v) {
        $values = is_array($v) ? $v : [$v]; // Ensure $v is an array
        $combined = $defaultValues; // Create base array with defaults
        
        // Override with provided values in order: label, filter, edit, align, fw
        if (isset($values[0])) $combined['label'] = $values[0];
        if (isset($values[1])) $combined['filter'] = $values[1];
        if (isset($values[2])) $combined['edit'] = $values[2];
        if (isset($values[3])) $combined['align'] = $values[3];
        if (isset($values[4])) $combined['fw'] = $values[4];        // ✅ Fixed: [4]
        $arr[$k] = $combined;
    }
    // yaha filter ke hisab se sirf selected column hi return kiya ja raha h
    // agar filter me comma separated, keyword nahi h to wo new array me asign nahi hoga 
    if($keyword != ''){
        $newArr = [];
        foreach ($arr as $k =>  $assoc) { 
            if(in_array($keyword, explode(',', $assoc['filter']))){
                $newArr[$k] = $assoc;
            }            
        }
        return $newArr;
    }
   
    /*$configTemp = [   
        'sl' => ['SL', 'fee', false, 'c'],
        'fee' => ['Fee', 'fee', false,'r'],
    ];
    $config = [
        'sl' => ['label' => 'SL', 'filter' => 'fee', 'edit' => false, 'align' => 'c'],
        'fee' => ['label' => 'Fee', 'filter' => 'fee', 'edit' => false, 'align' => 'r'],
    ];*/  
    //$loop[] = array_intersect_key($temp, array_flip(array_keys($configTemp)));   //break;
    return $arr;
}

function smsTable($resData = [], $divId = '') {
    $thead = $resData['thead'] ?? null;
    $tbody = $resData['tbody'] ?? null;
    $role = $resData['role'] ?? 'admin';
    $checkbox = $resData['checkbox'] ?? true;
    $skip_keys = $resData['skip_keys'] ?? [];

    if (!$thead || !$tbody) {
        echo "<div class='text-center p-3 fw-bold'>No data available</div>";
        return;
    }
    $pk = '';
    if (array_key_exists('pk', $thead)) { $pk = 'pk';    }
    else if(array_key_exists('user_id', $thead)) { $pk = 'user_id';    }
    else if(array_key_exists('email', $thead)) { $pk = 'email';    }
    else if(array_key_exists('username', $thead)) { $pk = 'username';    }
    else if(array_key_exists('id', $thead)) { $pk = 'id';    }
    else if(array_key_exists('sl', $thead)) { $pk = 'sl';    }

    $alignMap = ['c' => 'text-center', 'l' => 'text-start', 'r' => 'text-end'];

    $keys = array_keys($thead);
    
    echo "<div class='table-responsive' id='{$divId}'>";  //Thead Print start
    echo "<table class='smsTbl table table-striped table-bordered align-middle'>";
    echo "<thead><tr>";
    
    if ($checkbox) {       echo "<th class='text-center' nowrap='nowrap'>Select</th>";    }

    foreach ($keys as $key) {
        if(in_array($key, $skip_keys)){ continue; }
        $config = $thead[$key];
        $align = $alignMap[$config['align']] ?? 'text-start';
        echo "<th class='{$align}' style='cursor:pointer' nowrap='nowrap' data-key='{$key}'>";
        //echo htmlspecialchars($config['label']) . " <span style='font-size:0.7rem'>↕</span>";
        echo htmlspecialchars($config['label']);
        echo "</th>";
    }
    echo "</tr></thead>";  // thead print end

    // Tbody Print Start
    echo "<tbody>";
    foreach ($tbody as $keyName => $row) {

        $pkv = htmlspecialchars($row[$pk] ?? '');
        echo "<tr>";
        if ($checkbox) {
            echo "<td class='text-center'><input type='checkbox' class='row-checkbox' value='{$pkv}'></td>";
        }

        foreach ($keys as $key) {

            if(in_array($key, $skip_keys)){ continue; }  

            $rowVal = $row[$key] ?? '';
            $align = $alignMap[$thead[$key]['align']] ?? 'text-start';

            // for buttons , input, select etc
            $payload = (is_array($rowVal) && isset($rowVal['payload'])) ? $rowVal['payload'] : '';
            $funcName = (is_array($rowVal) && isset($rowVal['func'])) ? $rowVal['func'] : 'na';
            $inputType = (is_array($rowVal) && isset($rowVal['type'])) ? $rowVal['type'] : 'text';
            $inputVal = (is_array($rowVal) && isset($rowVal['value'])) ? $rowVal['value'] : '';
            $selectOption = (is_array($rowVal) && isset($rowVal['option'])) ? $rowVal['option'] : [];

            if (str_ends_with($key, '_del')) {
                $rowVal = "<button class='btn btn-sm btn-danger' data-payload='{$payload}' data-func='{$funcName}'>Delete</button>";
            }
            if (str_ends_with($key, '_edit')) {
                $rowVal = "<button class='btn btn-sm btn-primary' data-payload='{$payload}' data-func='{$funcName}'>Edit</button>";
            }
            if (str_ends_with($key, '_input')) {
                $rowVal = "<input type='{$inputType}' class='form-control' value='{$inputVal}' data-payload='{$payload}' data-func='{$funcName}'>";
            }
            if (str_ends_with($key, '_select')) {

                $optionHtml = '<option value="">-- Select --</option>';
                if (is_array($selectOption)) {
                    foreach ($selectOption as $option) {
                        $selected = $option['selected'] ? 'selected' : '';
                        $optionHtml .= "<option value='{$option['value']}'>{$option['label']}</option>";
                    }
                }

                $rowVal = "<select class='form-select form-select-sm' data-payload='{$payload}' data-func='{$funcName}'>
                                {$optionHtml}
                        </select>";
            }

            echo "<td class='{$align}' nowrap='nowrap'>{$rowVal}</td>";
        }
        echo "</tr>";
    }
    echo "</tbody></table>";
    echo "</div>";
}







const apiAxios = axios.create({ withCredentials: true });

const statusCodeHandlers = {
  // 2xx Success (type: 'info' ya default 'log')
  200: (data) => onConsole({ msg: "200 OK: Successful.", data }),
  201: (data) => onConsole({ msg: "201 Created: Successfully created.", data }),
  204: (data) => onConsole({ msg: "204 No Content: Processed, but no content.", data }),

  // 3xx Redirection (type: 'warn')
  301: (data) => onConsole({ msg: "301 Moved Permanently: Resource moved.", data, type: "warn" }),
  302: (data) => onConsole({ msg: "302 Found: Resource temporarily elsewhere.", data, type: "warn" }),
  304: (data) => onConsole({ msg: "304 Not Modified: Cached resource used.", data, type: "warn" }),

  // 4xx Client Errors (type: 'error' / 'warn')
  400: (data) => onConsole({ msg: "400 Bad Request: Bad syntax.", data, type: "error" }),
  401: (data) => onConsole({ msg: "401 Unauthorized: Authentication required.", data, type: "warn" }),
  403: (data) => onConsole({ msg: "403 Forbidden: Access denied.", data, type: "warn" }),
  404: (data) => onConsole({ msg: "404 Not Found: Resource not found.", data, type: "error" }),
  405: (data) => onConsole({ msg: "405 Method Not Allowed: HTTP method invalid.", data, type: "error" }),
  408: (data) => onConsole({ msg: "408 Request Timeout: Server timed out.", data, type: "error" }),
  409: (data) => onConsole({ msg: "409 Conflict: Request conflict.", data, type: "error" }),
  410: (data) => onConsole({ msg: "410 Gone: Resource no longer available.", data, type: "error" }),

  // 5xx Server Errors (type: 'error')
  500: (data) => onConsole({ msg: "500 Internal Server Error: Server error.", data, type: "error" }),
  501: (data) => onConsole({ msg: "501 Not Implemented: Server method unrecognized.", data, type: "error" }),
  502: (data) => onConsole({ msg: "502 Bad Gateway: Invalid response from upstream.", data, type: "error" }),
  503: (data) => onConsole({ msg: "503 Service Unavailable: Server temporarily down.", data, type: "error" }),
  504: (data) => onConsole({ msg: "504 Gateway Timeout: Response timeout.", data, type: "error" })
};

const handleStatus = (status, data = null) => {
  if (statusCodeHandlers[status]) {   statusCodeHandlers[status](data);  } 
  else {  onConsole({ msg: `Unhandled Status Code: ${status}`,  data: data, type: 'warn' });  }
};

// Request Interceptor
apiAxios.interceptors.request.use(function(config) {

    const token = localStorage.getItem('jwt_token');
    if (token) {
        config.headers.Authorization = 'Bearer ' + token;
    }

    const page_no = localStorage.getItem('page_no');
    if (page_no) {
        config.headers['page_no'] = page_no;
        localStorage.removeItem('page_no');
    }

    const admin_domain_key = localStorage.getItem('admin_domain_key');
    if (admin_domain_key) {
        config.headers['admin_domain_key'] = admin_domain_key;
    }

    return config;
}, function(error) {
    return Promise.reject(error);
});

// Response Interceptor
apiAxios.interceptors.response.use(
    function(response) {
        handleStatus(response.status,response);
        const newToken = response.headers['x-new-token'];
        if (newToken) {
            localStorage.setItem('jwt_token', newToken);
        }
        return response;
    },
    function(error) {
        if (error.response) {
            if (error.response.status === 401) {
                localStorage.removeItem('jwt_token');    window.location.href = 'login.html';
            }
            handleStatus(error.response.status, error);
        } else if (error.request) {  
            onConsole({msg:`🚨 [Network Error]: No response received from server.`,data: error});
        } else {
            onConsole({msg:`🚨 [Axios Setup Error]:`, data: error});
        }
        return Promise.reject(error);
    }
);

async function axiosSubmit(requestObject, path, method = "POST", onProgress = null) {
    try {
		btnsLoading(false);  // disable buttons
        let formData = await getFormData(requestObject); // compress image also
		if(formData == null){	 return { status: "error", message: "Form Data is null at Axios Submit" };		}
		const action = formData.get("action") || 'na';
	    const _method = (formData.get("_method") || method).toUpperCase();	
        //formData.append('_method', 'PUT');

        let newPath = path;		//<meta name="nodejs" content="true">
        const nodejs = (document.querySelector('meta[name="nodejs"]')?.getAttribute('content') === "true");
        if (nodejs) {    newPath = `${path}/${action}`;      }
		
        let csrfToken = null;
		const laravel = (document.querySelector('meta[name="laravel"]')?.getAttribute('content') === "true");
		if (laravel) {  
            newPath = action;
            csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            if (!csrfToken) {   return { status: "error", message: `CSRF token missing in head <meta name="csrf-token" content="{{ csrf_token() }}">` };     }
        }
		if(action === 'na'){	 return { status: "error", message: `Please add action in Form : ${action}` };		}
		
        // Decide payload type
        let payload = null;
        let headers = {};
        if(csrfToken){     headers['X-CSRF-TOKEN'] = csrfToken;    }
      
        const methodLower = _method.toLowerCase();
        if (["post", "put", "patch"].includes(methodLower)) {
            // 1. Check karein ki FormData me koi bhi File object hai ya nahi
            let hasFile = false;
            for (let value of formData.values()) {
                if (value instanceof File && value.name) {   hasFile = true;    break;    }
            }

            if (hasFile) {
                payload = formData;
                delete headers["Content-Type"];   // Browser isko boundary ke sath khud set karega
            } else {
                payload = Object.fromEntries(formData);
                headers["Content-Type"] = "application/json";
            }
        }

        const config = {
            method: _method.toLowerCase(),
            url: newPath,
            headers,
            data: payload,
            onUploadProgress: function (progressEvent) {
                if (progressEvent.lengthComputable && typeof onProgress === 'function') {
                    const percent = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                    onProgress(percent);
                }
            }
        };

        const response = await apiAxios(config);
        return response.data; // ✅ return clean data

    } catch (error) {
        let myMsg = error.response?.data?.message || error.message || 'Request failed';
        let axiosMsg = error.message || 'Request failed';
        onConsole({msg:"Axios Request Error:", data: axiosMsg});
        return { status: "error", message: `Axios Error Message: ${myMsg}` };
    } finally {
        // ✅ Always runs
        onConsole({msg:"Axios Request finished"});
		btnsLoading(true); // enable buttons
    }
}

$(document).on("click", "button[type='submit'], input[type='submit']", async function (e) {

    let btn = this;
    let form = $(btn).closest("form");
    if (form.data("ajax") !== true) { return; }
	e.preventDefault();

	let onProgress = null;
	const progress = form.data("onProgress");
	if (progress && typeof window[progress] === "function") {
		onProgress = progress;
	}

    let form_id = form.attr("id");
    if (!form_id) {  onConsole({msg:"No form ID found, stopping submit inside auto onclick submit"}); return;  }

    let isValid = true;
    let firstError = '';

    form.find("[required]").each(function () {
        let value = $(this).val().trim();
        let fieldName = $(this).attr("name") || "This field";

        if (!value) {
            isValid = false;
            firstError = fieldName + " is required";
            $(this).focus();
            return false; // break loop
        }
    });

    if (!isValid) {   goMsg({ status: "warning", msg: firstError });    return;    }

    const askSubmit = form.data("ask");
	if (askSubmit == true) {
		const result = await dsAlert.question('Are you sure to proceed..??');
        if (result != 'yes') {   return;   } 
	}

    //let formData = new FormData(form[0]);
    let dataObj = { form_id }; // form data auto get by id
	
	//actionPath is a global variable
    try {
		const response = await axiosSubmit(dataObj, actionPath, "POST", onProgress);
		if(response.status === 'success' || response.status === true){
			const callbackName = form.data("callback");
			if (callbackName && typeof window[callbackName] === "function") {
				window[callbackName](response);  
				return; // stop propagation
			}
		}
		showError(form, response) // show error on form
		goMsg(response, dataObj);
	} catch (error) {
		onConsole(`Auto submit error : ${error.message}`);
		goMsg({ status: "error", message: "Opps!!! Something went wrong inside auto onclick submit " });
	} finally {
		//onConsole("Auto Request finished");
	}
});

// 1. CLICK EVENT (Only for Buttons, Links, Checkboxes, and Radios)
document.addEventListener('click', function (e) {
    const element = e.target.closest('.smsTbl button, .smsTbl a, .smsTbl input[type="checkbox"], .smsTbl input[type="radio"]');
    if (element) {     handleElementAction(element, 'click');    }
});

// 2. CHANGE EVENT (For Select Dropdowns and File Inputs)
document.addEventListener('change', function (e) {
    const element = e.target.closest('.smsTbl select');
    if (element) {      handleElementAction(element, 'change');    }
});

// 3. BLUR EVENT (For Text Inputs, Textareas, Email, Passwords etc.)
// Note: Blur event bubble nahi hota, isliye 3rd argument 'true' (event capture phase) zaroori hai!
document.addEventListener('blur', function (e) {
    const element = e.target.closest('.smsTbl input:not([type="checkbox"]):not([type="radio"]), .smsTbl textarea');
    if (element) {      handleElementAction(element, 'blur');    }
}, true); // 👈 Capturing phase active karta hai


function handleElementAction(element, eventType) {
    let payload = {};
    try {
        payload = JSON.parse(element.dataset.payload || '{}');
    } catch (err) {
        payload = element.dataset.payload || {};
    }
    const funName = element.dataset.func || 'na';

    // Current Element Value & Event Info Payload me Merge Karein
    payload._val = element.value;
    payload._type = eventType;
    
    if (element.type === 'checkbox' || element.type === 'radio') {
        payload._checked = element.checked;
    }

    let selectedText = '';
    if (element.tagName === 'SELECT') {
        selectedText = element.options[element.selectedIndex]?.text;
        payload.value = element.value;
    }
    
    if (element.tagName === 'INPUT' || element.tagName === 'TEXTAREA') {
        // Normal Inputs (Text, Email, Number, Hidden, etc.)
        //payload._id = element.id || '';
        //payload._name = element.name || '';
        payload.value = element.value;
        //payload._type = element.type; // 'text', 'email', 'number', etc.
        //payload._length = element.value.length; // Characters length check karne ke liye
    } 

    onConsole({ msg: `[${eventType.toUpperCase()}] Function Triggered: ${funName}` });
    onConsole({ msg: "Payload Data:", data: payload });

    if (funName !== 'na' && typeof window[funName] === 'function') {
        window[funName](payload, element);
    } else if (funName !== 'na') {
        alert(`Invalid data-fun: ${funName}. Please correct.`);
    }
}

/***************************** HELPING FUNC BELOW **********************************/
function onConsole({ msg, data = null, type = 'log' }) {
    if (typeof console[type] === 'function') {
        console[type](`${msg}:`, data);
    } else {
        console.log(`${msg}:`, data);
    }
}

function showError(form, response, reload = false) {
    let $form =$(form); 

    $form.find('.error').removeClass('invalid-feedback').html('');
	$form.find('input, select, textarea').removeClass('is-invalid');

    if (response.status === 'success' || response.status === true) {
        
    } else if (response.errors) {
        let errors = response.errors;

        $.each(errors, function (key, value) {
            // Escape special characters in key (like dots in array validation)
            let fieldKey = key.replace(/(:|\.\[\vert{}\]|,|=|@)/g, "\\$1");
            let $input =$form.find(`#${fieldKey}`);

            // Agar ID se na mile toh name attribute se search karein
            if ($input.length === 0) {    $input = $form.find(`[name="${key}"]`);     }

            $input.addClass('is-invalid').siblings('.error').addClass('invalid-feedback')
			.html(Array.isArray(value) ? value[0] : value);
        });
    }
	if (reload) {	window.location.reload();	}
}

function btnsLoading(action) {
  const btnsElements = document.getElementsByClassName("btns");
  const loadingElements = document.getElementsByClassName("loading");
  if (btnsElements) {
    for (let element of btnsElements) {
      element.style.display = action ? "none" : "block"; // Hide if action is true, show otherwise
    }
  }
  if (loadingElements) {
    for (let element of loadingElements) {
      element.style.display = action ? "block" : "none"; // Show if action is false, hide otherwise
    }
  }
}

function pubHref(params) {
    // Default values for href, target, and method
    const href = params.href || '';
    const target = params.target || '_self'; // _self is default to open in the same window
    const method = params.method || 'POST'; // Default method is POST
    const queryParams = params.queryParams || {}; // Ensure queryParams is an empty object if not in params
    const bodyParams = params.bodyParams || {};
    //alert(target);
    // Construct the query string from queryParams object if exists
    let queryString = '';
    if (Object.keys(queryParams).length > 0 && method === 'GET') {
        queryString = '?' + new URLSearchParams(queryParams).toString();
    }

    // Construct the full URL with query string for GET requests
    const fullUrl = href + queryString;

    if (method === 'GET') {
        // For GET request, just open the URL
        window.open(fullUrl, target);
    } else if (method === 'POST') {
        // For POST request, create a form dynamically and submit it
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = fullUrl;
        form.target = target;

        // Add bodyParams as hidden fields in the form
        Object.keys(bodyParams).forEach(key => {
        //Object.keys(queryString).forEach(key => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = bodyParams[key];
            form.appendChild(input);
        });

        // Append the form to the body and submit
        document.body.appendChild(form);
        form.submit();
    } else {
        console.error('Unsupported HTTP method');
    }
}

function goMsg(res, array = {}) {
    //if (res.reload) setTimeout(() => location.reload());
    //if (res.url) setTimeout(() => location.href = res.url);

    if(!res){    onConsole({msg: "No Response data in goMsg :", data: res});    return;  }
    //else{     onConsole({msg: "Response has data in goMsg :", data: res});    }

    if (res.alert === 'swal') {
		if(res.status === 'html' || res.status === 'data'){
			onConsole({msg: "Response data in goMsg :", data: res});
			return;
		}else{
            if (res.status === 'success' || res.status === true ) {   res.status = 'success';     }
            if (!['info', 'warning', 'error'].includes(res.status)) {     res.status = "error";    }
			Swal.fire({
				title: res.title || (res.status === 'success' ? 'Success!' : 'Alert'),
				text: res.message || res.msg || '',
				icon: res.status || 'info', // success, error, warning, info
				//timer: res.timer || 3000
			});
			
			// Agar reload true hai toh swal ke baad reload karein
			if (res.reload === "reload") {     setTimeout(() => location.reload(), res.timer || 1500);    }
			return; // Swal dikhane ke baad function yahan se exit ho jaye
		}
    }

    // Normal Logic
    if (res.status === 'html') {
        if (array.div) {      document.getElementById(array.div).innerHTML = res.html;      }
    } 
    else if (res.status === 'success' || res.status === true ) {
        res.status = 'success';
        toastalert.showToast(res);
        //if (res.reload === "reload") {      setTimeout(() => location.reload(), 1500);     }
    } 
    else if (res.status === 'data') {     onConsole({msg:"Response status data in goMsg:",data: res});   } 
    else { 
        if (!['info', 'warning', 'error'].includes(res.status)) {     res.status = "error";    }
         toastalert.showToast(res);
    }
}

async function getFormData(array) {
    let formData;

    if (array instanceof FormData) {
        formData = array;
    } else if ('form_id' in array) {
        const form = document.getElementById(array['form_id']);
        formData = new FormData(form);
    } else if (typeof array === 'object') {
        formData = new FormData();
        for (const key in array) {
            if (array.hasOwnProperty(key)) {
                formData.append(key, array[key]);
            }
        }
    } else {
        goMsg({status: "error", message: "Unable to create FormData"});
        return null;
    }
	//Jab aap await compressImages(formData) call karte ho,
	//Woh andar se formData.delete(key) karke purane image files hata deta hai,
	//Fir compressed versions ko formData.append(key, file, file.name) se dobara add kar deta hai.
	//Isliye jab aap return formData; likhte ho, aapko wahi updated FormData object milta hai jisme compressed images replace ho chuke hote hain.
    await compressImages(formData);
    // await Promise.resolve();
    //console.log(formData);
    return formData;
}

function compressImages(formData) {
    //ensureCompressorLoaded(); // 🚫 stops execution if missing
    const filesToCompress = {};
    for (let [key, value] of formData.entries()) {
        if (value instanceof File && value.type.startsWith("image/")) {
            (filesToCompress[key] ??= []).push(value);
        }
    }

    if (!Object.keys(filesToCompress).length) {      return Promise.resolve();    }
    const compressionPromises = [];

    Object.entries(filesToCompress).forEach(([key, files]) => {
        files.forEach(file => {
            compressionPromises.push(
                new Promise((resolve, reject) => {
                    try {
                        new Compressor(file, {
                            quality: 0.5,
                            success(result) {
                                const compressedFile = new File( [result], file.name, {type: result.type}	);
                                resolve({key, file: compressedFile});
                            },
                            error(err) {
                                const msg = `CompressorJS is not loaded.Include any valid CompressorJS CDN: https://cdn.jsdelivr.net/npm/compressorjs/dist/compressor.min.js`;
                                goMsg({ status: "error",  message: msg});
                                reject(err);
                            }
                        });
                    } catch (err) {
                        const msg = `CompressorJS is not loaded.Include any valid CompressorJS CDN: https://cdn.jsdelivr.net/npm/compressorjs/dist/compressor.min.js`;
                        goMsg({ status: "error",  message: msg});
                        reject(err);
                    }
                })
            );
        });
    });

    return Promise.all(compressionPromises).then(compressedFiles => {
        Object.keys(filesToCompress).forEach(key => formData.delete(key));
        compressedFiles.forEach(({ key, file }) => {
            formData.append(key, file, file.name);
        });
    });
}

async function myPostAsk(arr, path = '') {
    const result = await dsAlert.question('Are you sure to proceed..??');
    if (result === 'yes') {
        myPost(arr, path = '');
    } else if (result === 'no') {
       // dsAlert.warning('User cancelled action');
    } else {
      //  dsAlert.info('Action cancelled.');
    }
}

function myPostCheck(arr, path = '')  {
    return new Promise((resolve, reject) => {
        let checked = [];
        $(".check").each(function () {
          if ($(this).is(":checked")) {     checked.push($(this).val());       }
        });

        checked = checked.toString();
        if (checked !== "") {
          arr["checked"] = checked;
          myPost(arr, path)
            .then((data) => {    resolve(data);        })
            .catch((error) => {  reject(error);        });
        } else {
          goMsg({ status: "info", msg: "Please select at least one record" });
        }
    });
}

function myPostPass(array, path = "") {
    return new Promise((resolve, reject) => {
    Swal.fire({
      title: "Warning",
      text: "Enter Password",
      input: "password",
      icon: "success",
      showCancelButton: true,
    }).then((result) => {
      if (result.value) {
        array["password"] = result.value;
        myPost(array, path)
          .then((data) => {    resolve(data);      })
          .catch((error) => { reject(error);       });
      } else {
        reject("User cancelled or empty input.");
      }
    });
  });
}

function myPostInput(array, path = "") {
  return new Promise((resolve, reject) => {
    Swal.fire({
      title: "Warning",
      text: "Enter Remarks",
      input: "text",
      icon: "success",
      showCancelButton: true,
    }).then((result) => {
      if (result.value) {
        array["remarks"] = result.value;
        myPost(array, path)
          .then((data) => {     resolve(data);         })
          .catch((error) => {  reject(error);         });
      } else {
        reject("User cancelled or empty input.");
      }
    });
  });
}

function myPostDate(array, path = "") {
  return new Promise((resolve, reject) => {
    // Get current date in YYYY-MM-DD format
    const today = new Date().toISOString().split('T')[0];
    
    Swal.fire({
      title: "Select Date",
      text: "Choose a date",
      input: "date",
      inputValue: today, // Set default to today's date
      icon: "success",
      showCancelButton: true,
    }).then((result) => {
      if (result.value) {
        array["date"] = result.value;
        myPost(array, path)
          .then((data) => { resolve(data); })
          .catch((error) => { reject(error); });
      } else {
        reject("User cancelled or no date selected.");
      }
    });
  });
}

function myPost(arr, path = '') {
    let finalPath = path === '' ? actionPath : path;
    axiosSubmit(arr, finalPath)
    .then((response) => {
        if (arr?.callback && typeof window[arr.callback] === "function") {
            window[arr.callback](response);
        } else {
            goMsg(response, arr);
        }
    })
    .catch(() => {     goMsg({status:"error", msg:"Opps!!! Something went wrong in myPost"});    });
}
