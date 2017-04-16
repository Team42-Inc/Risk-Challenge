package oasix.middleware.service;

import java.util.ArrayList;
import java.util.Date;
import java.util.List;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Component;

import com.github.vanroy.springdata.jest.aggregation.impl.AggregatedPageImpl;

import oasix.middleware.model.Command;
import oasix.middleware.model.InvalidConnection;
import oasix.middleware.model.Log;
import oasix.middleware.repository.CommandRepository;
import oasix.middleware.repository.InvalidConnectionRepository;
import oasix.middleware.repository.LogsRepository;

@Component
public class LogAnalysisService {
	@Autowired
	LogsRepository LogsRepository;
	
	@Autowired
	CommandRepository commandsRepository;
	
	@Autowired
	InvalidConnectionRepository invalidConnectionsRepository;
	
	public void analyse(){
		List<Command> commands = new ArrayList<>();
		List<InvalidConnection> invalidConnections = new ArrayList<>();
		
		Iterable<Log> logs = (AggregatedPageImpl<Log>) LogsRepository.findAll();
				
		logs.forEach((l)->{
			// Sudo commands
			if(l.getMessage().contains("TTY")){
				//PWD=/home/ubuntu ; USER=root ; COMMAND=/bin/nano /etc/rsyslog.d/01-json-template.conf

				String message = l.getMessage();
				Date timestamp = l.getTimestamp();
				
				Pattern userPattern = Pattern.compile("USER=(.*);");
				Matcher userMatcher = userPattern.matcher(message);
				userMatcher.find();
				String user = userMatcher.group().replace("USER=", "");
				
				Pattern commandPattern = Pattern.compile("COMMAND=(.*)");
				Matcher commandMatcher = commandPattern.matcher(message);
				commandMatcher.find();
				String commandString = commandMatcher.group().replace("COMMAND=", "");
				
				Command command = new Command(l.getHost(), timestamp, user, commandString);
				commands.add(command);
			} 
			
			// Connection failure
			if(l.getMessage().contains("invalid user")){
				// input_userauth_request: invalid user admin [preauth]
				InvalidConnection invalidConnection = new InvalidConnection(l.getHost(), l.getTimestamp());
				invalidConnections.add(invalidConnection);
			} 
		});
		
		invalidConnectionsRepository.save(invalidConnections);
		commandsRepository.save(commands);
	}
	
	public void cleanup() {
	}
}
