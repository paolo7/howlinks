var label_width=400;
var label_height=120;
var loading=false;
var incremental_id=0;
function increase_brightness(hex, percent){
    // strip the leading # if it's there
    hex = hex.replace(/^\s*#|\s*$/g, '');

    // convert 3 char codes --> 6, e.g. `E0F` --> `EE00FF`
    if(hex.length == 3){
        hex = hex.replace(/(.)/g, '$1$1');
    }

    var r = parseInt(hex.substr(0, 2), 16),
        g = parseInt(hex.substr(2, 2), 16),
        b = parseInt(hex.substr(4, 2), 16);

    return '#' +
       ((0|(1<<8) + r + (256 - r) * percent / 100).toString(16)).substr(1) +
       ((0|(1<<8) + g + (256 - g) * percent / 100).toString(16)).substr(1) +
       ((0|(1<<8) + b + (256 - b) * percent / 100).toString(16)).substr(1);
}
function increase_darkness(hex, percent){
    // strip the leading # if it's there
    hex = hex.replace(/^\s*#|\s*$/g, '');

    // convert 3 char codes --> 6, e.g. `E0F` --> `EE00FF`
    if(hex.length == 3){
        hex = hex.replace(/(.)/g, '$1$1');
    }

    var r = parseInt(hex.substr(0, 2), 16),
        g = parseInt(hex.substr(2, 2), 16),
        b = parseInt(hex.substr(4, 2), 16);

    return '#' +
       ((0|(1<<8) + r + (256 - r) * percent / 100).toString(16)).substr(1) +
       ((0|(1<<8) + g + (256 - g) * percent / 100).toString(16)).substr(1) +
       ((0|(1<<8) + b + (256 - b) * percent / 100).toString(16)).substr(1);
}

var labelType, useGradients, nativeTextSupport, animate;
(function() {
  var ua = navigator.userAgent,
      iStuff = ua.match(/iPhone/i) || ua.match(/iPad/i),
      typeOfCanvas = typeof HTMLCanvasElement,
      nativeCanvasSupport = (typeOfCanvas == 'object' || typeOfCanvas == 'function'),
      textSupport = nativeCanvasSupport 
        && (typeof document.createElement('canvas').getContext('2d').fillText == 'function');
  //I'm setting this based on the fact that ExCanvas provides text support for IE
  //and that as of today iPhone/iPad current text support is lame
  labelType = (!nativeCanvasSupport || (textSupport && !iStuff))? 'Native' : 'HTML';
  nativeTextSupport = labelType == 'Native';
  useGradients = nativeCanvasSupport;
  animate = !(iStuff || !nativeCanvasSupport);
})();

var Log = {
  elem: false,
  write: function(text){
    /*if (!this.elem) 
      this.elem = document.getElementById('log');
    this.elem.innerHTML = text;
    this.elem.style.left = (500 - this.elem.offsetWidth / 2) + 'px';*/
  }
};


function init(main,exact){
		document.getElementById('graph').style.height=(window.innerHeight-125)+'px';
		document.getElementById('graph').style.width=($(window).width())+'px';
		
		
		document.getElementById('infovis').style.height=(window.innerHeight-125)+'px';
		document.getElementById('infovis').style.width=($(window).width())+'px';
		
		document.getElementById('infobox').style.top=(window.innerHeight-135)+'px';
		document.getElementById('loading').style.top=(window.innerHeight-135)+'px';
		
		var json= {
			id:'0---'+main,
			name:exact,
			annotation:'process',
			image:'',
			dbpedia:'',
			children:[],
		};
		//init Spacetree
		//Create a new ST instance
		var st = new $jit.ST({
			//id of viz container element
			injectInto: 'infovis',
			//set duration for the animation
			duration: 800,
			//set animation transition type
			transition: $jit.Trans.Quart.easeInOut,
			//set distance between node and its children
			levelDistance: 100,
			//enable panning
			Navigation: {
			  enable:true,
			  panning:true
			},
			//set node and edge styles
			//set overridable=true for styling individual
			//nodes or edges
			Node: {
				height: label_height,
				width: label_width,
				type: 'rectangle',
				annotation: 'method',
				image: '',
				dbpedia: '',
				color: '#aaa',
				overridable: true
			},
			
			Edge: {
				type: 'bezier',
				color: '#646464',
				lineWidth: 3,
				overridable: true
			},
			
			onBeforeCompute: function(node){
				Log.write("loading " + node.name);
			},
			
			onAfterCompute: function(){
				Log.write("done");
			},
			
			
			//This method is called on DOM label creation.
			//Use this method to add event handlers and styles to
			//your node.
			onCreateLabel: function(label, node){
				label.id = node.id;
				
				//image search
				var font_size=20;
				if(node.name.length>100) 
					font_size=15;
				if(node.name.length>200) {
					font_size=15;
					node.name=node.name.substr(0,200)+"[...]";
				}
				if(node.image=='') {
					label.innerHTML = "<div style=\"width:"+(label_width-20)+"px;height:"+(label_height-20)+"px;float:left;margin-top:10px;margin-left:10px;font-size:"+font_size+"px;\">"+node.name+"</div>";
				}
				else {
					label.innerHTML = "<div style=\"width:"+(label_width-label_height-20)+"px;height:"+(label_height-20)+"px;float:left;margin-top:10px;margin-left:10px;font-size:"+font_size+"px;\">"+node.name+"</div><div style=\"width:"+(label_height-20)+"px;height:"+(label_height-20)+"px;float:right;margin-top:10px;margin-right:10px;\"><img src=\""+node.image+"\" width=\""+(label_height-20)+"\" height=\""+(label_height-20)+"\"></div>";
				}
				label.onclick = function(){
				  st.onClick(node.id);
				};
				//set label styles
				var style = label.style;
				style.width = label_width + 'px';
				style.height = label_height + 'px';            
				style.cursor = 'pointer';
				style.color = 'white';
				style.fontSize = '12px';
				style.textAlign= 'left';
			},
			// when the user is clicking on the node, we are generating the children automatically if they dont exist
			// the goal is to update the json structure at each click
			
			request: function(nodeId, level, onComplete) {
				if(loading==true) return;
				loading=true;
				document.getElementById("infobox").style.visibility='hidden';
				document.getElementById("loading").style.visibility='visible';
				var i=0;
				var node = st.graph.getNode(nodeId);
				
				var node_link=node.id.split('---')[1].substr(39);
				node_link='<a href="'+node_link+'">'+node_link+'</a>';
				var dbpedia_link='<a href="'+node.dbpedia+'">'+node.dbpedia+'</a>';
				var text='<b><u>Info box</b></u></br></br>'
					+'URI: '+node_link+'</br></br>';
				if(node.dbpedia!=''&&node.dbpedia!='undefined') {
					text+='DBpedia: '+dbpedia_link;
				}
				
				document.getElementById("infobox").innerHTML=text;
				
				$.ajax({
				  method: 'get',
				  url: 'request.php',
				  data: {
					'nodeid': node.id,
					'dbpedia':node.dbpedia,
					'annotation':node.annotation
				  },
				  success: function(data) {
				  
						document.getElementById("loading").style.visibility='hidden';
						//alert(data);
						loading=false;
						// on attribut les nouveaux ids
						data=update_id(data);
						try {
						
							var subtree = eval('(' + data.replace(/id:\"([a-zA-Z0-9]+)\"/g, 
							function(all, match) {
								return "id:\"" + match + "_" + i + "\""  
							}) + ')');
							$jit.json.prune(subtree, level); i++;
							var ans = {
								'id': nodeId,
								'children': subtree.children
							};
							onComplete.onComplete(nodeId, ans);
							
							document.getElementById("loading").style.visibility='hidden';
						}
						catch(err) {
							alert(err);
						}
					}
				});
			},
			//This method is called right before plotting
			//a node. It's useful for changing an individual node
			//style properties before plotting it.
			//The data properties prefixed with a dollar
			//sign will override the global node style properties.
			onBeforePlotNode: function(node){
				//add some color to the nodes in the path between the
				//root node and the selected node.
				switch(node.annotation) {
					default:
					case 'process': 
						node.data.$color = "#265e00"; break;
					case 'method': 
						node.data.$color = "#781b86"; break;
					case 'supplier': 
						node.data.$color = "#a31021"; break;
					case 'supplied': 
						node.data.$color = "#025e9f"; break;
					case 'supplied_extension':
						node.data.$color = "#025e9f"; break;
					case 'step':
					case 'step_process':
						node.data.$color = "#ed7d31"; break;
					case 'requirement': 
						node.data.$color = "#70ad47"; break;
					case 'input': 
						node.data.$color = "#a9ce98"; break;
					case 'output': 
						node.data.$color = "#00cee1"; break;
				}  
				if (node.selected) {
					node.data.$color=increase_darkness(node.data.$color,35);
				}
			},
			
			Events: {
				enable: true,
				onClick: function(node, eventInfo, e) {
					if(node!=null)
						document.getElementById("infobox").style.visibility='hidden';
				}
			},
			//This method is called right before plotting
			//an edge. It's useful for changing an individual edge
			//style properties before plotting it.
			//Edge data proprties prefixed with a dollar sign will
			//override the Edge global style properties.
			onBeforePlotLine: function(adj){
				if (adj.nodeFrom.selected && adj.nodeTo.selected) {
					adj.data.$color = "#eed";
					adj.data.$lineWidth = 3;
				}
				else {
					delete adj.data.$color;
					delete adj.data.$lineWidth;
				}
			}
		});
		//load json data
		st.loadJSON(json);
		//compute node positions and layout
		st.compute();
		//optional: make a translation of the tree
		st.geom.translate(new $jit.Complex(-200, 0), "current");
		//emulate a click on the root node.
		st.onClick(st.root);
		//end
}
function update_id(data) {
	while(data.indexOf('"id":"http')>-1) {
		data=data.replace('"id":"http','"id":"'+(++incremental_id)+'---'+'http');
	}
	return data;
}
