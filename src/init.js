/**
 * @copyright Copyright (c) 2018 Joas Schilling <coding@schilljs.com>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

import Vue from 'vue'
import App from './App'

Vue.prototype.t = t
Vue.prototype.n = n
Vue.prototype.OC = OC
Vue.prototype.OCA = OCA

const searchBox = document.getElementsByClassName('searchbox')
const notificationsBell = document.createElement('div')
notificationsBell.setAttribute('id', 'notifications')

Array.prototype.map.call(searchBox, (el) => {
	if (el.nodeName !== 'FORM') {
		return
	}

	el.insertAdjacentHTML('afterend', notificationsBell.outerHTML)
})

export default new Vue({
	el: '#notifications',
	name: 'NotificationsRoot',
	render: h => h(App)
})
