document.addEventListener("DOMContentLoaded", (event) => {
	const clearCacheButton = document.querySelector(".irfs-base button.clear-cache");
	const notificationsWrapper = document.querySelector(".irfs-base .notifications");

	if (clearCacheButton != null) {
		clearCacheButton.addEventListener("click", function () {
			clearCacheButton.setAttribute("disabled", null);
			fetch(`${ImageReformsizer.root}/clear-cache/`, {
				headers: {
					'X-WP-Nonce': ImageReformsizer.nonce
				},
			})
				.then(r => {
					if(r.status == 200) {
						addNotification("Cache cleared", "success");
					} else {
						addNotification("Unexpected error", "error");
					}
				})
				.catch((e) => {
					addNotification("Unexpected error", "error");
				})
				.finally(() => {
					clearCacheButton.removeAttribute("disabled");
				});
		});
	}

	function addNotification(text, style = "info") {
		let notification = document.createElement("p");
		notification.innerHTML = text;

		let styleClass = "";
		switch (style) {
			case "success":
				styleClass = "border-emerald-600";
				break;
			case "warning":
				styleClass = "border-amber-600";
				break;
			case "error":
				styleClass = "border-rose-600";
				break;
			default:
				styleClass = "border-sky-600";
				break;
		}

		notification.className = `block max-w-full w-max p-2 my-0! rounded-sm border-2 bg-white shadow-md ${styleClass}`;

		notificationsWrapper.appendChild(notification);

		setTimeout(() => {
			notification.remove();
		}, 2000);
	}
});
