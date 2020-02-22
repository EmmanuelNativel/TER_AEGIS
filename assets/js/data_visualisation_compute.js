function choose_color(d, domain){
	var color = d3.scaleLinear()
	    .domain(domain)
	    .range(["yellow", "red"]);

	return color(d.average);
}


function get_level(data, lvl, father=false){
	/*
	retourne un tableau contenant toutes les exp_unit de data d'un niveau
	data : toutes les exp_unit d'un essai
	lvl : le niveau d'exp_unit que l'on cherche
	father : si il est à true, les exp_unit retournées soint doté d'un attribut sons supplémentaire
	*/
	var level = [];
	for (i=0; i<data.length; i++){
		if(data[i]['level']==lvl){
			if(father){
				data[i].sons = [];
			}
			level.push(data[i]);
		} else if(data[i]['level']>lvl){
			break;
		}
	}
	return level;
}

function make_tree_stage(lvl_base, lvl_up){
	/*
	retourne un tableau d'exp_unit du niveau lvl_up enrichies de leurs possibles fils du niveau lvl_base
	lvl_base : tableau des fils possibles
	lvl_up : tableau des pères possibles
	*/
	var orphans = [];
	var l2 = false;
	if(lvl_base[0]['level']===2){
		l2 = true;
	}
	for(var i=0; i<lvl_base.length; i++){
		var j=0;
		while(lvl_base[i]['father']!=lvl_up[j]['id'] && j<lvl_up.length-1){
			j++;
		}

		if(lvl_base[i]['father']==lvl_up[j]['id']){
			lvl_up[j].sons.push(lvl_base[i]);
			if(l2){
				stage_coords(lvl_up[j], lvl_base[i].x, lvl_base[i].y);
			}
		} else {
			orphans.push(lvl_base[i]);
		}			
	}
	
	return lvl_up.concat(orphans);
}

function make_tree(lvl_base, curr_lvl){
	/*
	retourne un arbre
	*/
	if(curr_lvl == 1){
		lvl_up = get_level(exp_units, curr_lvl, true);
		var watered_tree = make_tree_stage(lvl_base, lvl_up);
		return watered_tree;
	} else {
		lvl_up = get_level(exp_units, curr_lvl, true);
		var watered_tree = make_tree_stage(lvl_base, lvl_up);
		return make_tree(watered_tree, curr_lvl-1);
	}
}

function stage_coords(block, x, y){
	/*
	Cette fonction permet de mettre à jour les coordonnées d'un bloc de niveau 1 à partir des coordonnées d'un de ses fils de niveau 2
	block : exp_unit de niveau 1
	x, y : coordonnées de exp_unit de niveau 2 courrant
	*/
	if(!block.x && !block.y){
		block.x = x;
		block.y = y;
	} else {
		if(x > block.x){
			block.x = x;
		}

		if(y > block.y){
			block.y = y;
		}
	}
}

function enrich_and_flatten_tree(poor_tree){
	poor_tree.forEach(function callback(exp_unit){
		if(!exp_unit.sons || exp_unit.sons.length == 0){
			enriched_flattened_exp_units.push(exp_unit);
		} else {
			for(var j=0; j<exp_unit.sons.length; j++){
				if(!exp_unit.sons[j].x && !exp_unit.sons[j].y){
					exp_unit.sons[j].x = exp_unit.x;
					exp_unit.sons[j].y = exp_unit.y;
				}
				callback(exp_unit.sons[j]);
			}
			enriched_flattened_exp_units.push(exp_unit);
		}
	});
}

function make_drawable_map(){
	/*
	relis les observations àleurs coordonnées
	certaines observations peuvent être réalisée sur des exp_unit différentes, en étant tout de même placée aux même coordonnées
	on retourne alors un tableau d'éléments (box) composés de coordonnées et d'une valeur etant la moyenne des valeurs de toutes les observations ayant été faites à ces coordonnées
	*/

	drawable_data = [];
	observations.forEach(function (obs){
		tmp = enriched_flattened_exp_units.find(function (exp_unit){
			return exp_unit.id == obs.id;
		}); //récupère l'exp_unit où l'observation a été réalisée

		index = drawable_data.findIndex(function (box){
			return (tmp.x == box.x && tmp.y == box.y);
		}); //vérifie si il existe déjà une box ayant ces coordonnées

		if(index != -1){
			//la box existe
			//on enrichit la box avec la nouvelle valeur de l'observation
			drawable_data[index].obs_group.push(obs);
			drawable_data[index].average = d3.mean(drawable_data[index].obs_group.map(val=>val.value));
		} else {
			//la box n'existe pas
			//on crée une nouvelle box contenant la valeur de l'observation
			// TODO expliquer map_id map_id: tmp.x +','+tmp.y, 
			var box = {x: tmp.x, y: tmp.y, obs_group: [obs], average: obs.value};
			drawable_data.push(box);
		}
	});
}

var exp_units = [];
var enriched_flattened_exp_units = [];
var observations = [];
var drawable_data = [];

var infoBox = d3.select("#info");

/*
2016_WP4_Parents_G2MARS_BCNAM_Diaphen
2014_WP3_16gen_Diaphen
2013_WP3_4gen_Diaphen
*/
$.ajax({
  dataType: "json",
//  url: "http://localhost/daphne-master/index.php/trials/get_trial_coords/2016_WP4_Parents_G2MARS_BCNAM_Diaphen",
  url: window.location.origin + "/index.php/trials/get_trial_coords/2016_WP4_Parents_G2MARS_BCNAM_Diaphen",
  success: function(data){
  	exp_units = data;
  	enrich_and_flatten_tree(make_tree(get_level(exp_units, 4), 3));
  }
});