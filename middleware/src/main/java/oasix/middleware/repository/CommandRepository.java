package oasix.middleware.repository;

import java.util.List;

import org.springframework.data.elasticsearch.repository.ElasticsearchCrudRepository;

import oasix.middleware.model.Command;

public interface CommandRepository extends ElasticsearchCrudRepository<Command, String> {
	List<Command> findByHost(String host);
}