package oasix.middleware;

import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;
import java.util.List;

import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.boot.context.event.ApplicationReadyEvent;
import org.springframework.context.ApplicationListener;
import org.springframework.stereotype.Component;

import oasix.middleware.service.AnalysisService;
import oasix.middleware.service.Commandservice;
import oasix.middleware.service.ConnectionStatsService;
import oasix.middleware.service.LogAnalysisService;

@Component
public class ApplicationStartup implements ApplicationListener<ApplicationReadyEvent> {
	// Define the logger object for this class
	private final Logger log = LoggerFactory.getLogger(this.getClass());

	@Autowired
	AnalysisService aggregationService;
	
	@Autowired
	LogAnalysisService sshConnectionStatsService;
	
	@Autowired
	ConnectionStatsService connectionStatsService;
	
	@Autowired
	Commandservice commandService;

	/**
	 * This event is executed as late as conceivably possible to indicate that
	 * the application is ready to service requests.
	 */
	@Override
	public void onApplicationEvent(final ApplicationReadyEvent event) {
		log.info("Application startup");
		
		sshConnectionStatsService.analyse();
		
		log.info("Cleaning up");
		aggregationService.cleanup();
		connectionStatsService.cleanup();
		
		log.info("Generating data");
		List<String> servers = new  ArrayList<>();
		servers.add("202.123.27.113"); //www.govmu.org
		servers.add("202.123.27.113"); //ta.gov-mu.org
		servers.add("196.27.64.122"); //www.mra.mu
		servers.add("10.0.2.85");
		
		for(String server : servers){
			log.info("Analysing " + server);
			aggregationService.analyse(server, new Date());
			
			log.info("Generating connection stats " + server);
			Date dt = new Date();
			for(int i = 0; i < 360; i++){
				Calendar c = Calendar.getInstance(); 
				c.setTime(dt); 
				c.add(Calendar.DATE, -i);
				dt = c.getTime();
				
				connectionStatsService.analyse(server, new Date());
			}
			
			commandService.aggregate(server);
		}
		
	}

}