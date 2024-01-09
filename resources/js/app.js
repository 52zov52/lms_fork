import './bootstrap';
import Alpine from 'alpinejs'
import mask from '@alpinejs/mask'
import moment from 'moment';
import Pikaday from 'pikaday';
 
Alpine.plugin(mask)

window.Pikaday = Pikaday;
window.moment = moment;