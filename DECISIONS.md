# Decisions

Keep this short — bullet points are fine. We care about the reasoning, not the
prose.

---

## 1. The bookmark list

What was actually wrong with it, and what did you change?

> What was wrong is that the query used here $bookmarks = Bookmark::query()->with('tags')->get(); would bring all the bookmakrs with their tags which would cause a memory overload. What we need to do is filter the query itself so that we reduce the amount of returned rows which reduces the load on the memory. To fix this, I combined the later if statements for the search and the tag using the laravel syntax for it to create one large query that can filter early on what we are getting from the database

Was there anything you noticed but deliberately did not fix? Why?

> Currently the front end and UI needs to change in order to work with simple paginate which in theory should work better with large amount of data.

---

## 2. Keeping the tag counts correct

`tags.bookmarks_count` is a denormalised counter: the real answer lives in the
`bookmark_tag` pivot table, but we store a copy on `tags` so the sidebar does not
have to aggregate on every page load. Once bookmarks can be archived, that copy
can drift from the truth.

**Which approach did you take, and why?**

> To do so, I added a function in the model for archiving and unarchiving. In one go, we check the status of archived or not then directly update, if update is returning True, it would mean that the archiving procedure succeeded and as such we decrement the values of bookmark_count for all the relevant tags.Otherwise, when getting a false, it would mean that the update did not happen and as such we dont have to update the values of bookmark_count.  This would insure that the database is only accessed once, and only when archive or unarchive is actually needed.

**Name at least one approach you considered and rejected, and what made you
reject it.**

> Check if the bookmark is archived or not in the database then do another query to update. I But in case two very fast calls for the api happen and both are asking for archiving, this may cause the bookmark_count to increment twice because the both queries when checking the database would recieve a response saying it was not archived.

**We have 25,000 bookmarks today. At 2.5 million, what breaks first in the
approach you picked?**

> What would break is the pagenator because the data is large and to improve it we can use simple paginator, but that would need some extra changes. I added the simplePaginator changes in comments, but it would still require more changes for the front-end which I did not have to do

---

## 3. Anything you interpreted, assumed or skipped

Ambiguities you resolved yourself, corners you cut, things you would do first if
you had another hour.

> For the archived bookmarks checkbox, it would only do the main bookmark tab, but the tags still show the number of unarchived tags. I needed more time to do finish the implementation. With more time I can complete the the simple paginator implementation. Front-end needs much more polishing but didnt focus on that. when archiving or unarchiving the page needs to be reloaded manually instead of it getting updated directly ( also front-end )
I neede to cut corners to get the seeding working on my device. I would not consider any corners were cut. However, I treated the code provided in the controller as a main idea and converted the logic into laravel instead for the query building

---

## 4. Tools

Which AI tools did you use, and what for? There is no wrong answer here — we ask
because it helps us have a better conversation about the code, not because we
are scoring it.

> I am using perplexity AI, the way I use it is generally as a technical support as asking what could be the errors based on, once I know where to look  then I manually check for any errors. When I run into issues or require specific syntax related to laravel then I use it to support with debugging.

Was there anywhere it steered you wrong, or suggested something you rejected?

> I had issues with the seeding of the data at first. It did not run directly when I used docker compose, so I had to comment the seeding line in .entrypoint.sh then seed the data from the command line.
The tests were not working as well off the bat, so I skipped running and rerunning the tests, I manually made sure everything is working by using postman for the API and the web end point for the front end.
