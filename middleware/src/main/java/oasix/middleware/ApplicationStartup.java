package oasix.middleware;

import java.util.Date;

import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.boot.context.event.ApplicationReadyEvent;
import org.springframework.context.ApplicationListener;
import org.springframework.stereotype.Component;

import oasix.middleware.service.AnalysisService;
import oasix.middleware.service.Commandservice;

@Component
public class ApplicationStartup implements ApplicationListener<ApplicationReadyEvent> {
	// Define the logger object for this class
	private final Logger log = LoggerFactory.getLogger(this.getClass());

	@Autowired
	AnalysisService aggregationService;
	
	@Autowired
	Commandservice commandService;

	/**
	 * This event is executed as late as conceivably possible to indicate that
	 * the application is ready to service requests.
	 */
	@Override
	public void onApplicationEvent(final ApplicationReadyEvent event) {
		log.info("Application startup");
		aggregationService.cleanup();
		
		aggregationService.analyse("www.govmu.org", new Date());
		aggregationService.analyse("ta.gov-mu.org", new Date());
		aggregationService.analyse("www.mra.mu", new Date());
		
		commandService.aggregate("gov");
	}

}